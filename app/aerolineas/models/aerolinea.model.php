<?php

namespace App\Aerolineas\Models;

class Aerolinea
{
    public int $id;
    public string $nombre;
    public string $descripcion;
    public string $codigoIata;
    /** @var array{id: int, nombre: string, codigo: string} */
    public array $pais;
    /** @var array{id: int, nombre: string, apellido: string}|null */
    public ?array $ceo;
    public bool $activa;

    public function __construct(
        int $id,
        string $nombre,
        string $descripcion,
        string $codigoIata,
        array $pais,
        ?array $ceo,
        bool $activa
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->codigoIata = strtoupper($codigoIata);
        $this->pais = $pais;
        $this->ceo = $ceo;
        $this->activa = $activa;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'codigoIata' => $this->codigoIata,
            'pais' => $this->pais,
            'ceo' => $this->ceo,
            'activa' => $this->activa,
        ];
    }
}
