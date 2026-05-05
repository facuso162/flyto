<?php

namespace App\Auth\Repositories;

use PDO;
use App\Auth\Models\TipoUsuario;

require_once __DIR__ . '/../models/tipo-usuario.model.php';

class TipoUsuarioRepository
{
    private PDO $pdo;

    public function __construct(
        PDO $pdo
    ) {
        $this->pdo = $pdo;
    }

    public function findByNombre(string $nombre): ?TipoUsuario {
        $sql = "
            SELECT id, nombre
            FROM tipos_usuario
            WHERE nombre = :nombre
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([
            ':nombre' => $nombre
        ]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new TipoUsuario(
            id: (int) $row['id'],
            nombre: $row['nombre']
        );
    }
}