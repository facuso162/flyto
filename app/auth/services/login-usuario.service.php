<?php

namespace App\Auth\Services;

use App\Auth\Dtos\LoginUsuarioDTO;
use App\Auth\Repositories\UsuarioRepository;
use App\Auth\Services\SessionService;

require_once __DIR__ . '/../dtos/login-usuario.dto.php';
require_once __DIR__ . '/../repositories/usuario.repository.php';
require_once __DIR__ . '/session.service.php';

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

    public function execute(LoginUsuarioDTO $dto): void {
        $usuario = $this->usuarioRepository->findByEmail($dto->email);

        if (!$usuario) {
            throw new \Exception('Credenciales inválidas.', 401);
        }

        if (!password_verify($dto->password, $usuario->claveHash)) {
            throw new \Exception('Credenciales inválidas.', 401);
        }

        if (!$usuario->emailVerificado) {
            throw new \Exception('Debes verificar tu cuenta antes de iniciar sesión', 403);
        }

        if (!$usuario->activo) {
            throw new \Exception('Usuario inactivo', 403);
        }

        $this->sessionService->login([
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'email' => $usuario->email,
            'tipo_usuario_id' => $usuario->tipoUsuario->id,
            'tipo_usuario_nombre' => $usuario->tipoUsuario->nombre
        ]);
    }
}