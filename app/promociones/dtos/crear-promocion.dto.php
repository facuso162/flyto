<?php

namespace App\Promociones\Dtos;

class CrearPromocionDto
{
    public string $descripcion;
    public int $descuento;

    public function __construct(string $descripcion, int $descuento)
    {
        $this->descripcion = $descripcion;
        $this->descuento = $descuento;
    }
}
