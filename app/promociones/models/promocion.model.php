<?php

namespace App\Promociones\Models;

class Promocion
{
    public ?int $id;
    public int $aerolineaId;
    public string $descripcion;
    public float $descuento;
    public \DateTime $fechaCreacion;
    public \DateTime $fechaFin;
    public ?\DateTime $fechaAprobacion;
    public bool $activa;

    public function __construct(
        ?int $id,
        int $aerolineaId,
        string $descripcion,
        float $descuento,
        \DateTime $fechaCreacion,
        \DateTime $fechaFin,
        ?\DateTime $fechaAprobacion,
        bool $activa
    ) {
        $this->id = $id;
        $this->aerolineaId = $aerolineaId;
        $this->descripcion = $descripcion;
        $this->descuento = $descuento;
        $this->fechaCreacion = $fechaCreacion;
        $this->fechaFin = $fechaFin;
        $this->fechaAprobacion = $fechaAprobacion;
        $this->activa = $activa;
    }

    public function estado(): string
    {
        if ($this->fechaFin < new \DateTime()) {
            return 'expirada';
        }
        return $this->activa ? 'aprobada' : 'pendiente';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'aerolineaId' => $this->aerolineaId,
            'descripcion' => $this->descripcion,
            'descuento' => $this->descuento,
            'fechaCreacion' => $this->fechaCreacion->format('Y-m-d H:i:s'),
            'fechaFin' => $this->fechaFin->format('Y-m-d H:i:s'),
            'fechaAprobacion' => $this->fechaAprobacion ? $this->fechaAprobacion->format('Y-m-d H:i:s') : null,
            'activa' => $this->activa,
            'estado' => $this->estado(),
        ];
    }
}