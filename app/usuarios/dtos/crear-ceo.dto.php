<?php

namespace App\Usuarios\Dtos;

class CrearCeoDto
{
    public function __construct(
        public string $nombre,
        public string $apellido,
        public string $email,
        public string $password,
        public int $aerolineaId
    ) {
    }
}
