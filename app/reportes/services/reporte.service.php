<?php

namespace App\Reportes\Services;

use App\Reportes\Models\ReporteVentas;
use App\Reportes\Models\ReporteOcupacion;
use App\Reportes\Repositories\ReporteRepository;

require_once __DIR__ . '/../models/reporte-ventas.model.php';
require_once __DIR__ . '/../models/reporte-ocupacion.model.php';
require_once __DIR__ . '/../repositories/reporte.repository.php';

class ReporteService
{
    public function __construct(
        private ReporteRepository $reporteRepository
    ) {
    }

    public function generarReporteVentas(int $ceoId): ReporteVentas
    {
        $inicioPeriodo = new \DateTimeImmutable('first day of this month 00:00:00');
        $finPeriodo = $inicioPeriodo->modify('first day of next month 00:00:00');

        return $this->reporteRepository->generarReporteVentas($ceoId, $inicioPeriodo, $finPeriodo);
    }

    public function generarReporteOcupacion(int $ceoId): ReporteOcupacion
    {
        $inicioPeriodo = new \DateTimeImmutable('first day of this month 00:00:00');
        $finPeriodo = $inicioPeriodo->modify('first day of next month 00:00:00');

        return $this->reporteRepository->generarReporteOcupacion($ceoId, $inicioPeriodo, $finPeriodo);
    }
}
