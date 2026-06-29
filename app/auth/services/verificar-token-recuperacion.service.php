<?php

namespace App\Auth\Services;

use App\Auth\Dtos\VerificarTokenRecuperacionDTO;
use App\Auth\Repositories\UsuarioRepository;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../dtos/verificar-token-recuperacion.dto.php';
require_once __DIR__ . '/../repositories/usuario.repository.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class VerificarTokenRecuperacionService
{
    private UsuarioRepository $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function execute(VerificarTokenRecuperacionDTO $dto): void
    {
        $usuario = $this->usuarioRepository->findByTokenRecuperacion($dto->token);

        if (!$usuario) {
            throw new HttpException('El codigo de recuperacion no es valido.', 404, ['field' => 'token']);
        }

        if ($usuario->tokenExpiracion !== null && $usuario->tokenExpiracion < new \DateTime()) {
            throw new HttpException('El codigo de recuperacion expiro. Solicita uno nuevo.', 400, ['field' => 'token']);
        }
    }
}
