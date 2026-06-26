<?php

namespace App\Promociones\Models;

use App\Aerolineas\Models\Aerolinea;

require_once __DIR__ . '/../../aerolineas/models/aerolinea.model.php';

class Promocion
{
    public ?int $id;
    public string $descripcion;
    public float $descuento;
    public \DateTime $fechaCreacion;
    public ?\DateTime $fechaAprobacion;
    public ?\DateTime $fechaFin;
    /** @var array{id: int, descripcion: string} */
    public array $estado;
    public Aerolinea $aerolinea;
    /** @var array{id: int, nombre: string, apellido: string} */
    public array $ceo;
    public bool $activa;

    public function __construct(
        ?int $id,
        string $descripcion,
        float $descuento,
        \DateTime $fechaCreacion,
        ?\DateTime $fechaAprobacion,
        ?\DateTime $fechaFin,
        array $estado,
        Aerolinea $aerolinea,
        array $ceo,
        bool $activa
    ) {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->descuento = $descuento;
        $this->fechaCreacion = $fechaCreacion;
        $this->fechaAprobacion = $fechaAprobacion;
        $this->fechaFin = $fechaFin;
        $this->estado = $estado;
        $this->aerolinea = $aerolinea;
        $this->ceo = $ceo;
        $this->activa = $activa;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'descripcion' => $this->descripcion,
            'descuento' => $this->descuento,
            'fechaCreacion' => $this->fechaCreacion->format('Y-m-d H:i:s'),
            'fechaAprobacion' => $this->fechaAprobacion?->format('Y-m-d H:i:s'),
            'fechaFin' => $this->fechaFin?->format('Y-m-d H:i:s'),
            'estado' => $this->estado,
            'aerolinea' => $this->aerolinea->toArray(),
            'ceo' => $this->ceo,
            'activa' => $this->activa,
        ];
    }
}
