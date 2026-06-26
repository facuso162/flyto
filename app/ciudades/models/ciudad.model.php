<?php

namespace App\Ciudades\Models;

use App\Paises\Models\Pais;

require_once __DIR__ . '/../../paises/models/pais.model.php';

class Ciudad
{
    public int $id;
    public string $nombre;
    public string $abreviacion;
    public Pais $pais;

    public function __construct(
        int $id,
        string $nombre,
        string $abreviacion,
        Pais $pais
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->abreviacion = $abreviacion;
        $this->pais = $pais;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'abreviacion' => $this->abreviacion,
            'pais' => $this->pais->toArray(),
        ];
    }
}
