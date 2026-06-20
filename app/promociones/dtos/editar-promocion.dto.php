<?php

namespace App\Promociones\Dtos;

class EditarPromocionDto
{
    public int $id;
    public string $descripcion;
    public int $descuento;

    public function __construct(int $id, string $descripcion, int $descuento)
    {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->descuento = $descuento;
    }
}
