<?php

namespace App\Reportes\Models;

class AerolineaReporteVuelos
{
    public function __construct(
        public string $nombre,
        public int $totalVuelos,
        public float $ocupacionPromedio
    ) {
    }
}

class ReporteVuelosAdmin
{
    /**
     * @param AerolineaReporteVuelos[] $topAerolineas
     */
    public function __construct(
        public \DateTimeImmutable $periodo,
        public \DateTimeImmutable $generadoEn,
        public int $totalVuelos,
        public float $ocupacionPromedioGlobal,
        public int $vuelosOcupacionAlta,
        public float $porcentajeVuelosOcupacionAlta,
        public int $vuelosOcupacionBaja,
        public float $porcentajeVuelosOcupacionBaja,
        public int $totalAsientosDisponibles,
        public int $totalAsientosVendidos,
        public array $topAerolineas
    ) {
    }
}
