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

    public function show(array $params, array $query, string $layoutPath): void
    {
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
