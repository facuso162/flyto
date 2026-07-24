<?php

namespace App\Perfil\Services;

use App\Auth\Models\Usuario;
use App\Auth\Repositories\UsuarioRepository;
use App\Perfil\Dtos\EditarPerfilDTO;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../auth/repositories/usuario.repository.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../dtos/editar-perfil.dto.php';

class PerfilService
{
    public function __construct(private UsuarioRepository $usuarioRepository)
    {
    }

    public function obtenerUsuario(int $usuarioId): Usuario
    {
        $usuario = $this->usuarioRepository->findById($usuarioId);

        if ($usuario === null) {
            throw new HttpException('No pudimos encontrar tu cuenta.', 404);
        }

        return $usuario;
    }

    public function editarDatos(EditarPerfilDTO $dto): void
    {
        $this->obtenerUsuario($dto->usuarioId);
        $this->usuarioRepository->updateProfileData(
            $dto->usuarioId,
            $dto->nombre,
            $dto->apellido,
            $dto->telefono
        );
    }

    public function verificarContrasenaActual(int $usuarioId, string $contrasena): void
    {
        $usuario = $this->obtenerUsuario($usuarioId);

        if (!password_verify($contrasena, $usuario->claveHash)) {
            throw new HttpException(
                'La contrasena actual no es valida.',
                401,
                ['field' => 'current_password']
            );
        }
    }

    public function cambiarContrasena(int $usuarioId, string $contrasena): void
    {
        $this->obtenerUsuario($usuarioId);
        $this->usuarioRepository->updatePassword(
            $usuarioId,
            password_hash($contrasena, PASSWORD_DEFAULT)
        );
    }
}
