<?php

namespace App\Reportes\Models;

class VueloReporteVentas
{
    public function __construct(
        public string $codigoVuelo,
        public string $origen,
        public string $destino,
        public int $reservas,
        public float $ingresos
    ) {
    }
}

class ReporteVentas
{
    /**
     * @param VueloReporteVentas[] $topVuelos
     */
    public function __construct(
        public \DateTimeImmutable $periodo,
        public \DateTimeImmutable $generadoEn,
        public int $totalReservasCompletadas,
        public int $totalReservasDelMes,
        public float $ingresosTotales,
        public int $reservasCanceladas,
        public float $porcentajeCanceladas,
        public float $montoCancelaciones,
        public array $topVuelos
    ) {
    }
}
