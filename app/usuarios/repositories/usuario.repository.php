<?php

namespace App\Usuarios\Repositories;

use App\Aerolineas\Models\Aerolinea;
use App\Usuarios\Models\Usuario;
use PDO;

require_once __DIR__ . '/../../aerolineas/models/aerolinea.model.php';
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
                a.id AS aerolinea_id, a.nombre AS aerolinea_nombre,
                a.descripcion AS aerolinea_descripcion, a.codigo_iata,
                a.pais_id AS aerolinea_pais_id, p.nombre AS aerolinea_pais_nombre,
                p.codigo AS aerolinea_pais_codigo,
                a.ceo_id AS aerolinea_ceo_id, a.activa AS aerolinea_activa
             FROM usuarios u
             INNER JOIN tipos_usuarios tu ON tu.id = u.tipo_usuario_id
             LEFT JOIN aerolineas a ON a.ceo_id = u.id
             LEFT JOIN paises p ON p.id = a.pais_id
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
            aerolinea: $row['aerolinea_id'] ? new Aerolinea(
                id: (int) $row['aerolinea_id'],
                nombre: (string) $row['aerolinea_nombre'],
                descripcion: (string) $row['aerolinea_descripcion'],
                codigoIata: (string) $row['codigo_iata'],
                pais: [
                    'id' => (int) $row['aerolinea_pais_id'],
                    'nombre' => (string) $row['aerolinea_pais_nombre'],
                    'codigo' => (string) $row['aerolinea_pais_codigo'],
                ],
                ceo: $row['aerolinea_ceo_id'] === null ? null : [
                    'id' => (int) $row['aerolinea_ceo_id'],
                    'nombre' => (string) $row['nombre'],
                    'apellido' => (string) $row['apellido'],
                ],
                activa: (bool) $row['aerolinea_activa']
            ) : null
        );
    }
}
