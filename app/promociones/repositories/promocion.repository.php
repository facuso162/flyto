<?php

namespace App\Promociones\Repositories;

use App\Aerolineas\Models\Aerolinea;
use App\Paises\Models\Pais;
use App\Promociones\Models\Promocion;
use PDO;

require_once __DIR__ . '/../models/promocion.model.php';
require_once __DIR__ . '/../../aerolineas/models/aerolinea.model.php';
require_once __DIR__ . '/../../paises/models/pais.model.php';

class PromocionRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(Promocion $promocion): void
    {
        $sql = 'INSERT INTO promociones (
                    descripcion, descuento, fecha_creacion, fecha_aprobacion,
                    fecha_fin, estado_id, aerolinea_id, activa
                ) VALUES (
                    :descripcion, :descuento, :fecha_creacion, :fecha_aprobacion,
                    :fecha_fin, :estado_id, :aerolinea_id, :activa
                )';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->parameters($promocion));
        $promocion->id = (int) $this->pdo->lastInsertId();
    }

    public function update(Promocion $promocion): void
    {
        $sql = 'UPDATE promociones SET
                    descripcion = :descripcion,
                    descuento = :descuento,
                    fecha_creacion = :fecha_creacion,
                    fecha_aprobacion = :fecha_aprobacion,
                    fecha_fin = :fecha_fin,
                    estado_id = :estado_id,
                    aerolinea_id = :aerolinea_id,
                    activa = :activa
                WHERE id = :id';

        $parameters = $this->parameters($promocion);
        $parameters[':id'] = $promocion->id;
        $this->pdo->prepare($sql)->execute($parameters);
    }

    /** @param int[] $estadosExclusivosIds */
    public function requestActivation(
        Promocion $promocion,
        int $estadoInactivaId,
        array $estadosExclusivosIds
    ): void {
        $estadosExclusivosIds = array_values(array_unique(array_map('intval', $estadosExclusivosIds)));
        $placeholders = implode(', ', array_fill(0, count($estadosExclusivosIds), '?'));

        $this->pdo->beginTransaction();

        try {
            $lock = $this->pdo->prepare(
                'SELECT id FROM promociones WHERE aerolinea_id = ? FOR UPDATE'
            );
            $lock->execute([$promocion->aerolinea->id]);

            $sql = 'UPDATE promociones
                    SET fecha_fin = NULL,
                        fecha_aprobacion = NULL,
                        estado_id = ?
                    WHERE aerolinea_id = ?
                      AND id <> ?
                      AND estado_id IN (' . $placeholders . ')';
            $parameters = [
                $estadoInactivaId,
                $promocion->aerolinea->id,
                (int) $promocion->id,
                ...$estadosExclusivosIds,
            ];

            $this->pdo->prepare($sql)->execute($parameters);
            $this->update($promocion);
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    public function updateEditableFields(int $id, string $descripcion, float $descuento): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE promociones SET descripcion = :descripcion, descuento = :descuento WHERE id = :id'
        );
        $stmt->execute([
            ':id' => $id,
            ':descripcion' => $descripcion,
            ':descuento' => $descuento,
        ]);
    }

    public function updateEditableFieldsAndDeactivate(Promocion $promocion): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE promociones
             SET descripcion = :descripcion,
                 descuento = :descuento,
                 estado_id = :estado_id,
                 fecha_fin = NULL,
                 fecha_aprobacion = NULL
             WHERE id = :id'
        );
        $stmt->execute([
            ':id' => $promocion->id,
            ':descripcion' => $promocion->descripcion,
            ':descuento' => $promocion->descuento,
            ':estado_id' => $promocion->estado['id'],
        ]);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM promociones WHERE id = :id')->execute([':id' => $id]);
    }

    /** @return Promocion[] */
    public function getAll(): array
    {
        $stmt = $this->pdo->query($this->selectSql() . ' ORDER BY p.fecha_creacion DESC, p.id DESC');

        return array_map(fn (array $row): Promocion => $this->mapRow($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getById(int $id): ?Promocion
    {
        $stmt = $this->pdo->prepare($this->selectSql() . ' WHERE p.id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRow($row) : null;
    }

    /** @return Promocion[] */
    public function getByEstado(string $estado): array
    {
        $stmt = $this->pdo->prepare($this->selectSql() . ' WHERE ep.nombre = :estado ORDER BY p.fecha_creacion DESC');
        $stmt->execute([':estado' => $estado]);

        return array_map(fn (array $row): Promocion => $this->mapRow($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /** @return Promocion[] */
    public function getByCeoId(int $ceoId): array
    {
        $stmt = $this->pdo->prepare($this->selectSql() . ' WHERE a.ceo_id = :ceo_id ORDER BY p.fecha_creacion DESC');
        $stmt->execute([':ceo_id' => $ceoId]);

        return array_map(fn (array $row): Promocion => $this->mapRow($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getActivaByCeoId(int $ceoId): ?Promocion
    {
        $stmt = $this->pdo->prepare(
            $this->selectSql()
            . " WHERE a.ceo_id = :ceo_id AND ep.nombre = 'activa' AND p.activa = 1"
            . ' AND (p.fecha_fin IS NULL OR p.fecha_fin >= NOW())'
            . ' ORDER BY p.fecha_aprobacion DESC, p.id DESC LIMIT 1'
        );
        $stmt->execute([':ceo_id' => $ceoId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRow($row) : null;
    }

    /** @return array{id: int, descripcion: string}|null */
    public function getEstadoByDescripcion(string $descripcion): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, nombre FROM estados_promociones WHERE nombre = :nombre LIMIT 1');
        $stmt->execute([':nombre' => $descripcion]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? ['id' => (int) $row['id'], 'descripcion' => (string) $row['nombre']] : null;
    }

    /** @return array{id: int, nombre: string, apellido: string}|null */
    public function getCeoById(int $ceoId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.id, u.nombre, u.apellido
             FROM usuarios u
             INNER JOIN tipos_usuarios tu ON tu.id = u.tipo_usuario_id
             WHERE u.id = :id
               AND tu.nombre = 'ceo'
             LIMIT 1"
        );
        $stmt->execute([':id' => $ceoId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? [
            'id' => (int) $row['id'],
            'nombre' => (string) $row['nombre'],
            'apellido' => (string) $row['apellido'],
        ] : null;
    }

    private function selectSql(): string
    {
        return 'SELECT
                    p.id, p.descripcion, p.descuento, p.fecha_creacion,
                    p.fecha_aprobacion, p.fecha_fin, p.activa,
                    ep.id AS estado_id, ep.nombre AS estado_descripcion,
                    a.id AS aerolinea_id, a.codigo_iata, a.nombre AS aerolinea_nombre,
                    a.descripcion AS aerolinea_descripcion, a.pais_id AS aerolinea_pais_id,
                    aerolinea_pais.nombre AS aerolinea_pais_nombre,
                    aerolinea_pais.codigo AS aerolinea_pais_codigo,
                    a.ceo_id AS aerolinea_ceo_id,
                    u.nombre AS aerolinea_ceo_nombre,
                    u.apellido AS aerolinea_ceo_apellido,
                    a.activa AS aerolinea_activa,
                    u.id AS ceo_id, u.nombre AS ceo_nombre, u.apellido AS ceo_apellido
                FROM promociones p
                INNER JOIN estados_promociones ep ON ep.id = p.estado_id
                INNER JOIN aerolineas a ON a.id = p.aerolinea_id
                INNER JOIN paises aerolinea_pais ON aerolinea_pais.id = a.pais_id
                LEFT JOIN usuarios u ON u.id = a.ceo_id';
    }

    private function parameters(Promocion $promocion): array
    {
        return [
            ':descripcion' => $promocion->descripcion,
            ':descuento' => $promocion->descuento,
            ':fecha_creacion' => $promocion->fechaCreacion->format('Y-m-d H:i:s'),
            ':fecha_aprobacion' => $promocion->fechaAprobacion?->format('Y-m-d H:i:s'),
            ':fecha_fin' => $promocion->fechaFin?->format('Y-m-d H:i:s'),
            ':estado_id' => $promocion->estado['id'],
            ':aerolinea_id' => $promocion->aerolinea->id,
            ':activa' => $promocion->activa ? 1 : 0,
        ];
    }

    private function mapRow(array $row): Promocion
    {
        if (!$row['ceo_id']) {
            throw new \RuntimeException('La promocion no tiene un CEO asociado.');
        }

        return new Promocion(
            id: (int) $row['id'],
            descripcion: (string) $row['descripcion'],
            descuento: (float) $row['descuento'],
            fechaCreacion: new \DateTime($row['fecha_creacion']),
            fechaAprobacion: $row['fecha_aprobacion'] ? new \DateTime($row['fecha_aprobacion']) : null,
            fechaFin: $row['fecha_fin'] ? new \DateTime($row['fecha_fin']) : null,
            estado: ['id' => (int) $row['estado_id'], 'descripcion' => (string) $row['estado_descripcion']],
            aerolinea: new Aerolinea(
                id: (int) $row['aerolinea_id'],
                nombre: (string) $row['aerolinea_nombre'],
                descripcion: (string) $row['aerolinea_descripcion'],
                codigoIata: (string) $row['codigo_iata'],
                pais: new Pais(
                    id: (int) $row['aerolinea_pais_id'],
                    nombre: (string) $row['aerolinea_pais_nombre'],
                    codigo: (string) $row['aerolinea_pais_codigo']
                ),
                ceo: $row['aerolinea_ceo_id'] === null ? null : [
                    'id' => (int) $row['aerolinea_ceo_id'],
                    'nombre' => (string) $row['aerolinea_ceo_nombre'],
                    'apellido' => (string) $row['aerolinea_ceo_apellido'],
                ],
                activa: (bool) $row['aerolinea_activa']
            ),
            ceo: [
                'id' => (int) $row['ceo_id'],
                'nombre' => (string) $row['ceo_nombre'],
                'apellido' => (string) $row['ceo_apellido'],
            ],
            activa: (bool) $row['activa']
        );
    }
}
