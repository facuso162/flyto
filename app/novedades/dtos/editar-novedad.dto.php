<?php

namespace App\Novedades\Dtos;

class EditarNovedadDto
{
    public int $id;
    public string $titulo;
    public string $texto;
    public string $categoria;
    public \DateTime $fechaExpiracion;

    public function __construct(
        int $id,
        string $titulo,
        string $texto,
        string $categoria,
        \DateTime $fechaExpiracion
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->texto = $texto;
        $this->categoria = $categoria;
        $this->fechaExpiracion = $fechaExpiracion;
    }
}
