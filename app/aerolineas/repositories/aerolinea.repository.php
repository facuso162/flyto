<?php

namespace App\Aerolineas\Repositories;

use App\Aerolineas\Models\Aerolinea;
use App\Aerolineas\Dtos\CrearAerolineaDto;
use App\Paises\Models\Pais;
use PDO;

require_once __DIR__ . '/../dtos/crear-aerolinea.dto.php';
require_once __DIR__ . '/../models/aerolinea.model.php';
require_once __DIR__ . '/../../paises/models/pais.model.php';

class AerolineaRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return Aerolinea[]
     */
    public function getTodas(): array
    {
        $stmt = $this->pdo->query($this->selectBase() . ' ORDER BY a.nombre ASC');

        return array_map(
            fn (array $row): Aerolinea => $this->mapRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function getPorCeoId(int $ceoId): ?Aerolinea
    {
        $stmt = $this->pdo->prepare($this->selectBase() . ' WHERE a.ceo_id = :ceo_id LIMIT 1');
        $stmt->execute([':ceo_id' => $ceoId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRow($row) : null;
    }

    public function getPorId(int $id): ?Aerolinea
    {
        $stmt = $this->pdo->prepare($this->selectBase() . ' WHERE a.id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRow($row) : null;
    }

    public function existsByCodigoIata(string $codigoIata): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM aerolineas WHERE codigo_iata = :codigo_iata');
        $stmt->execute([':codigo_iata' => strtoupper($codigoIata)]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function existsByCodigoIataExcludingId(string $codigoIata, int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM aerolineas WHERE codigo_iata = :codigo_iata AND id <> :id'
        );
        $stmt->execute([
            ':codigo_iata' => strtoupper($codigoIata),
            ':id' => $id,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function crear(CrearAerolineaDto $dto): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO aerolineas (nombre, descripcion, codigo_iata, pais_id, ceo_id, activa)
             VALUES (:nombre, :descripcion, :codigo_iata, :pais_id, NULL, TRUE)'
        );

        $stmt->execute([
            ':nombre' => $dto->nombre,
            ':descripcion' => $dto->descripcion,
            ':codigo_iata' => strtoupper($dto->codigoIata),
            ':pais_id' => $dto->paisId,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function editar(int $id, CrearAerolineaDto $dto): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE aerolineas
             SET nombre = :nombre,
                 descripcion = :descripcion,
                 codigo_iata = :codigo_iata,
                 pais_id = :pais_id
             WHERE id = :id'
        );

        $stmt->execute([
            ':id' => $id,
            ':nombre' => $dto->nombre,
            ':descripcion' => $dto->descripcion,
            ':codigo_iata' => strtoupper($dto->codigoIata),
            ':pais_id' => $dto->paisId,
        ]);
    }

    private function selectBase(): string
    {
        return '
            SELECT
                a.id,
                a.nombre,
                a.descripcion,
                a.codigo_iata,
                a.pais_id,
                p.nombre AS pais_nombre,
                p.codigo AS pais_codigo,
                a.ceo_id,
                u.nombre AS ceo_nombre,
                u.apellido AS ceo_apellido,
                a.activa
            FROM aerolineas a
            INNER JOIN paises p ON p.id = a.pais_id
            LEFT JOIN usuarios u ON u.id = a.ceo_id
        ';
    }

    private function mapRow(array $row): Aerolinea
    {
        return new Aerolinea(
            id: (int) $row['id'],
            nombre: (string) $row['nombre'],
            descripcion: (string) $row['descripcion'],
            codigoIata: (string) $row['codigo_iata'],
            pais: new Pais(
                id: (int) $row['pais_id'],
                nombre: (string) $row['pais_nombre'],
                codigo: (string) $row['pais_codigo']
            ),
            ceo: $row['ceo_id'] === null ? null : [
                'id' => (int) $row['ceo_id'],
                'nombre' => (string) $row['ceo_nombre'],
                'apellido' => (string) $row['ceo_apellido'],
            ],
            activa: (bool) $row['activa']
        );
    }
}
