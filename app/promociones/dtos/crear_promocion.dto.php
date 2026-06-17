<?php

namespace App\Promociones\Dtos;

class CrearPromocionDto
{
    public function __construct(
        public readonly string $descripcion,
        public readonly float $descuento,
        public readonly \DateTime $fechaFin
    ) {}
}