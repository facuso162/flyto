<?php

namespace App\Auth\Repositories;

use PDO;
use App\Auth\Models\Usuario;
use App\Auth\Models\TipoUsuario;
use Throwable;

require_once __DIR__ . '/../models/usuario.model.php';
require_once __DIR__ . '/../models/tipo-usuario.model.php';

class UsuarioRepository
{
    private PDO $pdo;

    public function __construct(
        PDO $pdo
    ) {
        $this->pdo = $pdo;
    }

    public function register(Usuario $usuario): void {
        try {
            $this->pdo->beginTransaction();

            $this->create($usuario);

            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    private function create(Usuario $usuario): void {
        $sql = "
            INSERT INTO usuarios (
                nombre,
                apellido,
                email,
                telefono,
                clave_hash,
                tipo_usuario_id,
                activo,
                fecha_registro,
                email_verificado,
                token_verificacion,
                token_recupero,
                token_expiracion
            ) VALUES (
                :nombre,
                :apellido,
                :email,
                :telefono,
                :clave_hash,
                :tipo_usuario_id,
                :activo,
                :fecha_registro,
                :email_verificado,
                :token_verificacion,
                :token_recupero,
                :token_expiracion
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':nombre' => $usuario->nombre,
            ':apellido' => $usuario->apellido,
            ':email' => $usuario->email,
            ':telefono' => $usuario->telefono,
            ':clave_hash' => $usuario->claveHash,
            ':tipo_usuario_id' => $usuario->tipoUsuario->id, // TODO: que pasa si llega un tipo de usuario con id null?
            ':activo' => $usuario->activo ? 1 : 0,
            ':fecha_registro' => $usuario->fechaRegistro->format('Y-m-d H:i:s'),
            ':email_verificado' => $usuario->emailVerificado ? 1 : 0,
            ':token_verificacion' => $usuario->tokenVerificacion,
            ':token_recupero' => $usuario->tokenRecupero,
            ':token_expiracion' => $usuario->tokenExpiracion?->format('Y-m-d H:i:s')
        ]);

        $usuario->id = (int) $this->pdo->lastInsertId();
    }

    public function existsByEmail(string $email): bool {
        $sql = "
            SELECT 1
            FROM usuarios
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        return $stmt->fetchColumn() !== false;
    }

    public function findByTokenVerificacion(string $token): ?Usuario {
        $sql = "
            SELECT
                u.id,
                u.nombre,
                u.apellido,
                u.email,
                u.telefono,
                u.clave_hash,
                u.activo,
                u.fecha_registro,
                u.email_verificado,
                u.token_verificacion,
                u.token_recupero,
                u.token_expiracion,
                tu.id AS tipo_usuario_id,
                tu.nombre AS tipo_usuario_nombre
            FROM usuarios u
            JOIN tipos_usuarios tu ON u.tipo_usuario_id = tu.id
            WHERE u.token_verificacion = :token
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':token' => $token
        ]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $tipoUsuario = new TipoUsuario(
            id: (int) $row['tipo_usuario_id'],
            nombre: $row['tipo_usuario_nombre']
        );

        return new Usuario(
            id: (int) $row['id'],
            nombre: $row['nombre'],
            apellido: $row['apellido'],
            email: $row['email'],
            telefono: $row['telefono'],
            claveHash: $row['clave_hash'],
            tipoUsuario: $tipoUsuario,
            activo: (bool) $row['activo'],
            fechaRegistro: new \DateTime($row['fecha_registro']),
            emailVerificado: (bool) $row['email_verificado'],
            tokenVerificacion: $row['token_verificacion'],
            tokenRecupero: $row['token_recupero'],
            tokenExpiracion: $row['token_expiracion'] ? new \DateTime($row['token_expiracion']) : null
        );
    }

    public function findByEmail(string $email): ?Usuario {
        $sql = "
            SELECT
                u.id,
                u.nombre,
                u.apellido,
                u.email,
                u.telefono,
                u.clave_hash,
                u.activo,
                u.fecha_registro,
                u.email_verificado,
                u.token_verificacion,
                u.token_recupero,
                u.token_expiracion,
                tu.id AS tipo_usuario_id,
                tu.nombre AS tipo_usuario_nombre
            FROM usuarios u
            JOIN tipos_usuarios tu ON u.tipo_usuario_id = tu.id
            WHERE u.email = :email
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $tipoUsuario = new TipoUsuario(
            id: (int) $row['tipo_usuario_id'],
            nombre: $row['tipo_usuario_nombre']
        );

        return new Usuario(
            id: (int) $row['id'],
            nombre: $row['nombre'],
            apellido: $row['apellido'],
            email: $row['email'],
            telefono: $row['telefono'],
            claveHash: $row['clave_hash'],
            tipoUsuario: $tipoUsuario,
            activo: (bool) $row['activo'],
            fechaRegistro: new \DateTime($row['fecha_registro']),
            emailVerificado: (bool) $row['email_verificado'],
            tokenVerificacion: $row['token_verificacion'],
            tokenRecupero: $row['token_recupero'],
            tokenExpiracion: $row['token_expiracion'] ? new \DateTime($row['token_expiracion']) : null
        );
    }

    // TODO: que pasa si el usuario tiene id null?
    public function update(Usuario $usuario): void {
        $sql = "
            UPDATE usuarios SET
                nombre = :nombre,
                apellido = :apellido,
                email = :email,
                telefono = :telefono,
                clave_hash = :clave_hash,
                tipo_usuario_id = :tipo_usuario_id,
                activo = :activo,
                fecha_registro = :fecha_registro,
                email_verificado = :email_verificado,
                token_verificacion = :token_verificacion,
                token_recupero = :token_recupero,
                token_expiracion = :token_expiracion
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $usuario->id,
            ':nombre' => $usuario->nombre,
            ':apellido' => $usuario->apellido,
            ':email' => $usuario->email,
            ':telefono' => $usuario->telefono,
            ':clave_hash' => $usuario->claveHash,
            ':tipo_usuario_id' => $usuario->tipoUsuario->id, // TODO: que pasa si llega un tipo de usuario con id null?
            ':activo' => $usuario->activo ? 1 : 0,
            ':fecha_registro' => $usuario->fechaRegistro->format('Y-m-d H:i:s'),
            ':email_verificado' => $usuario->emailVerificado ? 1 : 0,
            ':token_verificacion' => $usuario->tokenVerificacion,
            ':token_recupero' => $usuario->tokenRecupero,
            ':token_expiracion' => $usuario->tokenExpiracion?->format('Y-m-d H:i:s')
        ]);
    }
}