<?php

namespace App\Paises\Repositories;

use App\Paises\Models\Pais;
use PDO;

require_once __DIR__ . '/../models/pais.model.php';

class PaisRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return Pais[]
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, nombre, codigo FROM paises ORDER BY nombre ASC'
        );

        return array_map(
            fn (array $row): Pais => $this->mapRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    private function mapRow(array $row): Pais
    {
        return new Pais(
            id: (int) $row['id'],
            nombre: (string) $row['nombre'],
            codigo: (string) $row['codigo']
        );
    }
}
