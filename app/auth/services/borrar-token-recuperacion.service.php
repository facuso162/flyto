<?php

namespace App\Auth\Services;

use App\Auth\Repositories\UsuarioRepository;

require_once __DIR__ . '/../repositories/usuario.repository.php';

class BorrarTokenRecuperacionService
{
    public function __construct(private UsuarioRepository $usuarioRepository)
    {
    }

    public function execute(int $usuarioId): void
    {
        $this->usuarioRepository->clearTokenRecuperacion($usuarioId);
    }
}
