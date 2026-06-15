<?php

namespace App\Novedades\Repositories;

use App\Novedades\Models\Novedad;
use PDO;

require_once __DIR__ . '/../models/novedad.model.php';

class NovedadRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(Novedad $novedad): void
    {
        $sql = "
            INSERT INTO novedades (
                titulo,
                texto,
                categoria,
                fechaPublicacion,
                fechaExpiracion
            ) VALUES (
                :titulo,
                :texto,
                :categoria,
                :fechaPublicacion,
                :fechaExpiracion
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':titulo' => $novedad->titulo,
            ':texto' => $novedad->texto,
            ':categoria' => $novedad->categoria,
            ':fechaPublicacion' => $novedad->fechaPublicacion->format('Y-m-d H:i:s'),
            ':fechaExpiracion' => $novedad->fechaExpiracion->format('Y-m-d H:i:s'),
        ]);

        $novedad->id = (int) $this->pdo->lastInsertId();
    }

    public function update(Novedad $novedad): void
    {
        $sql = "
            UPDATE novedades SET
                titulo = :titulo,
                texto = :texto,
                categoria = :categoria,
                fechaExpiracion = :fechaExpiracion
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $novedad->id,
            ':titulo' => $novedad->titulo,
            ':texto' => $novedad->texto,
            ':categoria' => $novedad->categoria,
            ':fechaExpiracion' => $novedad->fechaExpiracion->format('Y-m-d H:i:s'),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM novedades WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function findById(int $id): ?Novedad
    {
        $sql = "
            SELECT
                id,
                titulo,
                texto,
                categoria,
                fechaPublicacion,
                fechaExpiracion
            FROM novedades
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();

        return $row ? $this->mapRow($row) : null;
    }

    /**
     * @return Novedad[]
     */
    public function getUltimas(int $limit = 3): array
    {
        return $this->getVigentes($limit);
    }

    /**
     * @return Novedad[]
     */
    public function getVigentes(?int $limit = null): array
    {
        return $this->getAll(vigentes: true, limit: $limit);
    }

    /**
     * @return Novedad[]
     */
    public function getTodas(): array
    {
        return $this->getAll();
    }

    /**
     * @return Novedad[]
     */
    private function getAll(bool $vigentes = false, ?int $limit = null): array
    {
        $sql = "
            SELECT
                id,
                titulo,
                texto,
                categoria,
                fechaPublicacion,
                fechaExpiracion
            FROM novedades
        ";

        if ($vigentes) {
            $sql .= " WHERE fechaExpiracion > NOW()";
        }

        $sql .= " ORDER BY fechaPublicacion DESC, id DESC";

        if ($limit !== null) {
            $sql .= " LIMIT " . max(1, $limit);
        }

        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();

        return array_map(fn (array $row) => $this->mapRow($row), $rows);
    }

    private function mapRow(array $row): Novedad
    {
        return new Novedad(
            id: (int) $row['id'],
            titulo: $row['titulo'],
            texto: $row['texto'],
            categoria: $row['categoria'],
            fechaPublicacion: new \DateTime($row['fechaPublicacion']),
            fechaExpiracion: new \DateTime($row['fechaExpiracion'])
        );
    }
}
