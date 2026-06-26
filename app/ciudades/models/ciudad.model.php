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
    public int $paisId;
    public string $nombrePais;
    public string $codigoPais;

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
        $this->paisId = $pais->id;
        $this->nombrePais = $pais->nombre;
        $this->codigoPais = $pais->codigo;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'abreviacion' => $this->abreviacion,
            'paisId' => $this->paisId,
            'nombrePais' => $this->nombrePais,
            'codigoPais' => $this->codigoPais,
            'pais' => $this->pais->toArray(),
        ];
    }
}
