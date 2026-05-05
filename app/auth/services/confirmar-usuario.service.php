<?php

namespace App\Auth\Services;

use App\Auth\Repositories\UsuarioRepository;

require_once __DIR__ . '/../repositories/usuario.repository.php';

class ConfirmarUsuarioService
{
    private UsuarioRepository $usuarioRepository;

    public function __construct(
        UsuarioRepository $usuarioRepository
    ) {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function execute(string $token): void {
        $usuario = $this->usuarioRepository->findByTokenVerificacion($token);
    
        if (!$usuario) {
            throw new \Exception('Token de confirmación inválido', 400);
        }

        if ($usuario->emailVerificado) {
            throw new \Exception('El email ya ha sido verificado', 400);
        }

        $usuario->emailVerificado = true;
        $usuario->tokenVerificacion = null;

        $this->usuarioRepository->update($usuario);
    }
}
