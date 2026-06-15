<?php

namespace App\Novedades\Models;

class Novedad
{
    public ?int $id;
    public string $titulo;
    public string $texto;
    public string $categoria;
    public \DateTime $fechaPublicacion;
    public \DateTime $fechaExpiracion;

    public function __construct(
        ?int $id,
        string $titulo,
        string $texto,
        string $categoria,
        \DateTime $fechaPublicacion,
        \DateTime $fechaExpiracion
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->texto = $texto;
        $this->categoria = $categoria;
        $this->fechaPublicacion = $fechaPublicacion;
        $this->fechaExpiracion = $fechaExpiracion;
    }

    public function estado(): string
    {
        return $this->fechaExpiracion > new \DateTime() ? 'vigente' : 'expirada';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->id,
            'titulo' => $this->titulo,
            'texto' => $this->texto,
            'categoria' => $this->categoria,
            'fechaPublicacion' => $this->fechaPublicacion->format('Y-m-d H:i:s'),
            'fechaExpiracion' => $this->fechaExpiracion->format('Y-m-d H:i:s'),
            'estado' => $this->estado(),
        ];
    }
}
