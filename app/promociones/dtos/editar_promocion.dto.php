<?php

namespace App\Promociones\Dtos;

class EditarPromocionDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $descripcion,
        public readonly float $descuento,
        public readonly \DateTime $fechaFin
    ) {}
}