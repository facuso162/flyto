<?php

namespace App\Auth\Services;

use App\Auth\Dtos\LoginUsuarioDTO;
use App\Auth\Repositories\UsuarioRepository;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../dtos/login-usuario.dto.php';
require_once __DIR__ . '/../repositories/usuario.repository.php';
require_once __DIR__ . '/session.service.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class LoginUsuarioService
{
    private UsuarioRepository $usuarioRepository;
    private SessionService $sessionService;

    public function __construct(
        UsuarioRepository $usuarioRepository,
        SessionService $sessionService
    ) {
        $this->usuarioRepository = $usuarioRepository;
        $this->sessionService = $sessionService;
    }

    public function execute(LoginUsuarioDTO $dto): array
    {
        $usuario = $this->usuarioRepository->findByEmail($dto->email);

        if (!$usuario || !password_verify($dto->password, $usuario->claveHash)) {
            throw new HttpException('Credenciales invalidas.', 401);
        }

        if (!$usuario->emailVerificado) {
            throw new HttpException('Debes verificar tu cuenta antes de iniciar sesion.', 403);
        }

        if (!$usuario->activo) {
            throw new HttpException('Usuario inactivo.', 403);
        }

        $userData = [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'email' => $usuario->email,
            'telefono' => $usuario->telefono,
            'tipo_usuario' => [
                'id' => $usuario->tipoUsuario->id,
                'nombre' => $usuario->tipoUsuario->nombre
            ]
        ];

        $this->sessionService->login($userData);

        return $userData;
    }
}
