<?php

namespace App\Usuarios\Services;

use App\Usuarios\Repositories\UsuarioRepository;

require_once __DIR__ . '/../repositories/usuario.repository.php';

class UsuarioService
{
    public function __construct(private UsuarioRepository $usuarioRepository)
    {
    }

    public function getConfirmadosByTipo(string $tipo): array
    {
        return $this->usuarioRepository->getConfirmadosByTipo($tipo);
    }
}
