<?php

namespace App\Reportes\Controllers;

use App\Reportes\Services\ReporteService;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/reporte.service.php';

class AdminReporteVuelosPageController
{
    public function __construct(
        private ReporteService $reporteService,
        private ViewResponse $viewResponse
    ) {
    }

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->viewResponse->render(
            __DIR__ . '/../views/pages/admin-reporte-vuelos.page.php',
            'Reporte de vuelos - Panel Admin - Flyto',
            [
                'reporte' => $this->reporteService->generarReporteVuelosAdmin(),
            ],
            200,
            $layoutPath
        );
    }
}
