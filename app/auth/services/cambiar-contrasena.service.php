<?php

namespace App\Auth\Services;

use App\Auth\Dtos\CambiarContrasenaDTO;
use App\Auth\Repositories\UsuarioRepository;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../dtos/cambiar-contrasena.dto.php';
require_once __DIR__ . '/../repositories/usuario.repository.php';
require_once __DIR__ . '/verificar-token-recuperacion.service.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class CambiarContrasenaService
{
    public function __construct(
        private UsuarioRepository $usuarioRepository,
        private VerificarTokenRecuperacionService $verificarTokenRecuperacionService
    ) {
    }

    public function execute(CambiarContrasenaDTO $dto): void
    {
        $usuario = $this->verificarTokenRecuperacionService->validarUsuarioConToken($dto->usuarioId);

        if ((int) $usuario->id !== $dto->usuarioId) {
            throw new HttpException('El usuario de recuperacion no es valido.', 404, ['field' => 'usuario_id']);
        }

        $this->usuarioRepository->updatePasswordAndClearRecoveryToken(
            $dto->usuarioId,
            password_hash($dto->password, PASSWORD_DEFAULT)
        );
    }
}
