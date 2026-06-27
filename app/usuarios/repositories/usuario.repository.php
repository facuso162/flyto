<?php

namespace App\Usuarios\Repositories;

use App\Aerolineas\Models\Aerolinea;
use App\Paises\Models\Pais;
use App\Usuarios\Dtos\CrearCeoDto;
use App\Usuarios\Models\Usuario;
use PDO;
use Throwable;

require_once __DIR__ . '/../../aerolineas/models/aerolinea.model.php';
require_once __DIR__ . '/../../paises/models/pais.model.php';
require_once __DIR__ . '/../dtos/crear-ceo.dto.php';
require_once __DIR__ . '/../models/usuario.model.php';

class UsuarioRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /** @return Usuario[] */
    public function getConfirmadosByTipo(string $tipo): array
    {
        return $this->fetchByTipo($tipo, true);
    }

    /** @return Usuario[] */
    public function getByTipo(string $tipo): array
    {
        return $this->fetchByTipo($tipo, false);
    }

    public function existsByEmail(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);

        return $stmt->fetchColumn() !== false;
    }

    public function aerolineaDisponibleParaCeo(int $aerolineaId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM aerolineas WHERE id = :id AND ceo_id IS NULL LIMIT 1'
        );
        $stmt->execute([':id' => $aerolineaId]);

        return $stmt->fetchColumn() !== false;
    }

    public function getTipoUsuarioIdPorNombre(string $nombre): ?int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM tipos_usuarios WHERE nombre = :nombre LIMIT 1');
        $stmt->execute([':nombre' => $nombre]);

        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    public function crearCeo(CrearCeoDto $dto, int $tipoUsuarioId): int
    {
        try {
            $this->pdo->beginTransaction();

            $usuarioId = $this->insertarCeo($dto, $tipoUsuarioId);
            $this->asignarCeoAAerolinea($dto->aerolineaId, $usuarioId);

            $this->pdo->commit();

            return $usuarioId;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    /** @return Usuario[] */
    private function fetchByTipo(string $tipo, bool $soloConfirmados): array
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
             WHERE tu.nombre = :tipo' .
                ($soloConfirmados ? '
               AND u.activo = 1
               AND u.email_verificado = 1' : '') .
             '
             ORDER BY u.fecha_registro DESC, u.id DESC'
        );
        $stmt->execute([':tipo' => $tipo]);

        return array_map(
            fn (array $row): Usuario => $this->mapRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    private function insertarCeo(CrearCeoDto $dto, int $tipoUsuarioId): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO usuarios (
                nombre, apellido, email, telefono, clave_hash,
                tipo_usuario_id, activo, fecha_registro, email_verificado,
                token_verificacion, token_recupero, token_expiracion
             ) VALUES (
                :nombre, :apellido, :email, NULL, :clave_hash,
                :tipo_usuario_id, TRUE, NOW(), TRUE,
                NULL, NULL, NULL
             )'
        );

        $stmt->execute([
            ':nombre' => $dto->nombre,
            ':apellido' => $dto->apellido,
            ':email' => $dto->email,
            ':clave_hash' => password_hash($dto->password, PASSWORD_DEFAULT),
            ':tipo_usuario_id' => $tipoUsuarioId,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    private function asignarCeoAAerolinea(int $aerolineaId, int $usuarioId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE aerolineas
             SET ceo_id = :ceo_id
             WHERE id = :id AND ceo_id IS NULL'
        );
        $stmt->execute([
            ':id' => $aerolineaId,
            ':ceo_id' => $usuarioId,
        ]);

        if ($stmt->rowCount() !== 1) {
            throw new \RuntimeException('La aerolinea no pudo ser asignada al CEO.');
        }
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
                pais: new Pais(
                    id: (int) $row['aerolinea_pais_id'],
                    nombre: (string) $row['aerolinea_pais_nombre'],
                    codigo: (string) $row['aerolinea_pais_codigo']
                ),
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
