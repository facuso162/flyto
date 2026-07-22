<?php

namespace App\Reportes\Controllers;

use App\Auth\Services\SessionService;
use App\Reportes\Services\ReporteService;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/reporte.service.php';

class AdminReporteCeosPageController
{
    public function __construct(
        private ReporteService $reporteService,
        private SessionService $sessionService,
        private ViewResponse $viewResponse
    ) {
    }

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        // TODO - Analizar si es necesario este codigo de aca abajo
        $this->sessionService->getUser();

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/admin-reporte-ceos.page.php',
            'Reporte de CEOs - Panel Admin - Flyto',
            [
                'reporte' => $this->reporteService->generarReporteCeosAdmin(),
            ],
            200,
            $layoutPath
        );
    }
}
