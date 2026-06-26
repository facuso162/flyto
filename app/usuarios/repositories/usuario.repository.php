<?php

namespace App\Usuarios\Repositories;

use App\Usuarios\Models\Usuario;
use PDO;

require_once __DIR__ . '/../models/usuario.model.php';

class UsuarioRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /** @return Usuario[] */
    public function getConfirmadosByTipo(string $tipo): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                u.id, u.nombre, u.apellido, u.email, u.activo,
                u.fecha_registro, u.email_verificado,
                tu.nombre AS tipo_nombre,
                a.id AS aerolinea_id, a.nombre AS aerolinea_nombre, a.codigo_iata
             FROM usuarios u
             INNER JOIN tipos_usuarios tu ON tu.id = u.tipo_usuario_id
             LEFT JOIN aerolineas a ON a.ceo_id = u.id
             WHERE tu.nombre = :tipo
               AND u.activo = 1
               AND u.email_verificado = 1
             ORDER BY u.fecha_registro DESC, u.id DESC'
        );
        $stmt->execute([':tipo' => $tipo]);

        return array_map(
            fn (array $row): Usuario => $this->mapRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    private function mapRow(array $row): Usuario
    {
        return new Usuario(
            id: (int) $row['id'],
            nombre: (string) $row['nombre'],
            apellido: (string) $row['apellido'],
            email: (string) $row['email'],
            tipo: (string) $row['tipo_nombre'],
            activo: (bool) $row['activo'],
            fechaRegistro: new \DateTime($row['fecha_registro']),
            emailVerificado: (bool) $row['email_verificado'],
            aerolinea: $row['aerolinea_id'] ? [
                'id' => (int) $row['aerolinea_id'],
                'nombre' => (string) $row['aerolinea_nombre'],
                'codigoIata' => (string) $row['codigo_iata'],
            ] : null
        );
    }
}
