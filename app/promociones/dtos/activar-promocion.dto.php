<?php

namespace App\Promociones\Dtos;

class ActivarPromocionDto
{
    public int $id;
    public \DateTime $fechaFin;

    public function __construct(int $id, \DateTime $fechaFin)
    {
        $this->id = $id;
        $this->fechaFin = $fechaFin;
    }
}
