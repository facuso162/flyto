<?php

namespace App\Novedades\Dtos;

class CrearNovedadDto
{
    public string $titulo;
    public string $texto;
    public string $categoria;
    public \DateTime $fechaExpiracion;

    public function __construct(
        string $titulo,
        string $texto,
        string $categoria,
        \DateTime $fechaExpiracion
    ) {
        $this->titulo = $titulo;
        $this->texto = $texto;
        $this->categoria = $categoria;
        $this->fechaExpiracion = $fechaExpiracion;
    }
}
