<?php

namespace App\Reportes\Models;

class VueloReporteOcupacion
{
    public function __construct(
        public string $codigoVuelo,
        public string $origen,
        public string $destino,
        public int $asientosOcupados,
        public int $asientosDisponibles,
        public float $porcentajeOcupacion
    ) {
    }
}

class ReporteOcupacion
{
    /**
     * @param VueloReporteOcupacion[] $topVuelos
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
        public array $topVuelos
    ) {
    }
}
