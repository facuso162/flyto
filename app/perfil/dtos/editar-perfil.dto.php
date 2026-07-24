<?php

namespace App\Perfil\Dtos;

class EditarPerfilDTO
{
    public function __construct(
        public int $usuarioId,
        public string $nombre,
        public string $apellido,
        public ?string $telefono
    ) {
    }
}
