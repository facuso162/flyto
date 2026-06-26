<?php

namespace App\Promociones\Models;

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
    /** @var array{id: int, codigoIata: string, nombre: string} */
    public array $aerolinea;
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
        array $aerolinea,
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
            'aerolinea' => $this->aerolinea,
            'ceo' => $this->ceo,
            'activa' => $this->activa,
        ];
    }
}
