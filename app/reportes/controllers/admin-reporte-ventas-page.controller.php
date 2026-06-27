<?php

namespace App\Reportes\Controllers;

use App\Reportes\Services\ReporteService;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/reporte.service.php';

class AdminReporteVentasPageController
{
    public function __construct(
        private ReporteService $reporteService,
        private ViewResponse $viewResponse
    ) {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->viewResponse->render(
            __DIR__ . '/../views/pages/admin-reporte-ventas.page.php',
            'Reporte de ventas - Panel Admin - Flyto',
            [
                'reporte' => $this->reporteService->generarReporteVentasAdmin(),
            ],
            200,
            $layoutPath
        );
    }
}
