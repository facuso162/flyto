<?php

namespace App\Aerolineas\Dtos;

class CrearAerolineaDto
{
    public function __construct(
        public string $nombre,
        public string $codigoIata,
        public string $descripcion,
        public int $paisId
    ) {
    }
}
