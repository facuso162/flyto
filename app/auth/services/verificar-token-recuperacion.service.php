<?php

namespace App\Auth\Services;

use App\Auth\Dtos\VerificarTokenRecuperacionDTO;
use App\Auth\Models\Usuario;
use App\Auth\Repositories\UsuarioRepository;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../dtos/verificar-token-recuperacion.dto.php';
require_once __DIR__ . '/../models/usuario.model.php';
require_once __DIR__ . '/../repositories/usuario.repository.php';
require_once __DIR__ . '/borrar-token-recuperacion.service.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class VerificarTokenRecuperacionService
{
    public function __construct(
        private UsuarioRepository $usuarioRepository,
        private BorrarTokenRecuperacionService $borrarTokenRecuperacionService
    ) {
    }

    public function execute(VerificarTokenRecuperacionDTO $dto): Usuario
    {
        $usuario = $this->usuarioRepository->findByTokenRecuperacion($dto->token);

        if (!$usuario) {
            throw new HttpException('El codigo de recuperacion no es valido.', 404, ['field' => 'token']);
        }

        $this->validarTokenActivo($usuario, 'token');

        return $usuario;
    }

    public function validarUsuarioConToken(int $usuarioId): Usuario
    {
        $usuario = $this->usuarioRepository->findById($usuarioId);

        if (!$usuario) {
            throw new HttpException('El usuario de recuperacion no existe.', 404, ['field' => 'usuario_id']);
        }

        if ($usuario->tokenRecupero === null || trim($usuario->tokenRecupero) === '') {
            throw new HttpException('El usuario no tiene un codigo de recuperacion activo.', 400, ['field' => 'usuario_id']);
        }

        $this->validarTokenActivo($usuario, 'usuario_id');

        return $usuario;
    }

    private function validarTokenActivo(Usuario $usuario, string $field): void
    {
        if ($usuario->tokenExpiracion === null) {
            if ($usuario->id !== null) {
                $this->borrarTokenRecuperacionService->execute((int) $usuario->id);
            }

            throw new HttpException('El codigo de recuperacion no es valido. Solicita uno nuevo.', 400, ['field' => $field]);
        }

        if ($usuario->tokenExpiracion < new \DateTime()) {
            $this->borrarTokenRecuperacionService->execute((int) $usuario->id);

            throw new HttpException('El codigo de recuperacion expiro. Solicita uno nuevo.', 400, ['field' => $field]);
        }
    }
}
