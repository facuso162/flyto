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
        $sql = "INSERT INTO promociones (aerolineaId, descripcion, descuento, fechaCreacion, fechaFin, activa) 
                VALUES (:aerolineaId, :descripcion, :descuento, :fechaCreacion, :fechaFin, :activa)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':aerolineaId' => $promocion->aerolineaId,
            ':descripcion' => $promocion->descripcion,
            ':descuento' => $promocion->descuento,
            ':fechaCreacion' => $promocion->fechaCreacion->format('Y-m-d H:i:s'),
            ':fechaFin' => $promocion->fechaFin->format('Y-m-d H:i:s'),
            ':activa' => (int) $promocion->activa,
        ]);

        $promocion->id = (int) $this->pdo->lastInsertId();
    }

    public function update(Promocion $promocion): void
    {
        $sql = "UPDATE promociones SET descripcion = :descripcion, descuento = :descuento, fechaFin = :fechaFin, 
                fechaAprobacion = :fechaAprobacion, activa = :activa WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $promocion->id,
            ':descripcion' => $promocion->descripcion,
            ':descuento' => $promocion->descuento,
            ':fechaFin' => $promocion->fechaFin->format('Y-m-d H:i:s'),
            ':fechaAprobacion' => $promocion->fechaAprobacion ? $promocion->fechaAprobacion->format('Y-m-d H:i:s') : null,
            ':activa' => (int) $promocion->activa,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM promociones WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function findById(int $id): ?Promocion
    {
        $stmt = $this->pdo->prepare("SELECT * FROM promociones WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->mapRow($row) : null;
    }

    public function getPendientes(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM promociones WHERE activa = 0 ORDER BY fechaCreacion ASC");
        return array_map(fn ($row) => $this->mapRow($row), $stmt->fetchAll());
    }

    public function getByAerolinea(int $aerolineaId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM promociones WHERE aerolineaId = :aerolineaId ORDER BY id DESC");
        $stmt->execute([':aerolineaId' => $aerolineaId]);
        return array_map(fn ($row) => $this->mapRow($row), $stmt->fetchAll());
    }

    private function mapRow(array $row): Promocion
    {
        return new Promocion(
            id: (int) $row['id'],
            aerolineaId: (int) $row['aerolineaId'],
            descripcion: $row['descripcion'],
            descuento: (float) $row['descuento'],
            fechaCreacion: new \DateTime($row['fechaCreacion']),
            fechaFin: new \DateTime($row['fechaFin']),
            fechaAprobacion: $row['fechaAprobacion'] ? new \DateTime($row['fechaAprobacion']) : null,
            activa: (bool) $row['activa']
        );
    }
}