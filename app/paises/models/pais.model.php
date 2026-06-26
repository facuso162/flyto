<?php

namespace App\Paises\Models;

class Pais
{
    public int $id;
    public string $nombre;
    public string $codigo;

    public function __construct(
        int $id,
        string $nombre,
        string $codigo
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->codigo = strtoupper($codigo);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
        ];
    }
}
