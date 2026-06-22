<?php

namespace App\Promociones\Repositories;

use App\Promociones\Models\Promocion;
use PDO;

require_once __DIR__ . '/../models/promocion.model.php';

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
        $stmt = $this->pdo->prepare($this->selectSql() . ' WHERE LOWER(ep.nombre) = :estado ORDER BY p.fecha_creacion DESC');
        $stmt->execute([':estado' => strtolower($estado)]);

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
            . " WHERE a.ceo_id = :ceo_id AND LOWER(ep.nombre) = 'activa' AND p.activa = 1"
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
        $stmt = $this->pdo->prepare('SELECT id, nombre FROM estados_promociones WHERE LOWER(nombre) = :nombre LIMIT 1');
        $stmt->execute([':nombre' => strtolower($descripcion)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? ['id' => (int) $row['id'], 'descripcion' => (string) $row['nombre']] : null;
    }

    /** @return array{id: int, codigoIata: string, nombre: string}|null */
    public function getAerolineaByCeoId(int $ceoId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, codigo_iata, nombre FROM aerolineas WHERE ceo_id = :ceo_id LIMIT 1');
        $stmt->execute([':ceo_id' => $ceoId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? [
            'id' => (int) $row['id'],
            'codigoIata' => (string) $row['codigo_iata'],
            'nombre' => (string) $row['nombre'],
        ] : null;
    }

    private function selectSql(): string
    {
        return 'SELECT
                    p.id, p.descripcion, p.descuento, p.fecha_creacion,
                    p.fecha_aprobacion, p.fecha_fin, p.activa,
                    ep.id AS estado_id, ep.nombre AS estado_descripcion,
                    a.id AS aerolinea_id, a.codigo_iata, a.nombre AS aerolinea_nombre
                FROM promociones p
                INNER JOIN estados_promociones ep ON ep.id = p.estado_id
                INNER JOIN aerolineas a ON a.id = p.aerolinea_id';
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
            ':aerolinea_id' => $promocion->aerolinea['id'],
            ':activa' => $promocion->activa ? 1 : 0,
        ];
    }

    private function mapRow(array $row): Promocion
    {
        return new Promocion(
            id: (int) $row['id'],
            descripcion: (string) $row['descripcion'],
            descuento: (float) $row['descuento'],
            fechaCreacion: new \DateTime($row['fecha_creacion']),
            fechaAprobacion: $row['fecha_aprobacion'] ? new \DateTime($row['fecha_aprobacion']) : null,
            fechaFin: $row['fecha_fin'] ? new \DateTime($row['fecha_fin']) : null,
            estado: ['id' => (int) $row['estado_id'], 'descripcion' => (string) $row['estado_descripcion']],
            aerolinea: [
                'id' => (int) $row['aerolinea_id'],
                'codigoIata' => (string) $row['codigo_iata'],
                'nombre' => (string) $row['aerolinea_nombre'],
            ],
            activa: (bool) $row['activa']
        );
    }
}
