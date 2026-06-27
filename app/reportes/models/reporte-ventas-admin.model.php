<?php

namespace App\Reportes\Models;

class AerolineaReporteVentas
{
    public function __construct(
        public string $nombre,
        public int $reservas,
        public float $ingresos
    ) {
    }
}

class ReporteVentasAdmin
{
    /**
     * @param AerolineaReporteVentas[] $topAerolineas
     */
    public function __construct(
        public \DateTimeImmutable $periodo,
        public \DateTimeImmutable $generadoEn,
        public int $aerolineasActivas,
        public int $totalReservas,
        public float $ingresosTotales,
        public float $comision,
        public array $topAerolineas
    ) {
    }
}
