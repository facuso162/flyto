<?php

namespace App\Vuelos\Dtos;

class CrearVueloDto
{
    public function __construct(
        public string $codigoVuelo,
        public float $precio,
        public int $asientosDisponibles,
        public \DateTimeImmutable $fechaSalida,
        public \DateTimeImmutable $fechaLlegada,
        public int $origenCiudadId,
        public int $destinoCiudadId,
        public float $duracionHoras,
        public int $distanciaKm
    ) {
    }
}
