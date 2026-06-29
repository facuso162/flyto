<?php

namespace App\Auth\Dtos;

class CambiarContrasenaDTO
{
    public int $usuarioId;
    public string $password;

    public function __construct(int $usuarioId, string $password)
    {
        $this->usuarioId = $usuarioId;
        $this->password = $password;
    }
}
