<?php

namespace App\Reportes\Controllers;

use App\Auth\Services\SessionService;
use App\Reportes\Services\ReporteService;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/reporte.service.php';

class ReporteOcupacionPageController
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
        // TODO - No poner un fallback si no encuentra el usuario, lanzar un error
        $usuario = $this->sessionService->getUser() ?? [];
        // TODO - No poner un fallback si no encuentra el id, lanzar un error
        $ceoId = (int) ($usuario['id'] ?? 0);

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/reporte-ocupacion.page.php',
            'Reporte de ocupacion de vuelos - Panel CEO - Flyto',
            [
                'reporte' => $this->reporteService->generarReporteOcupacion($ceoId),
            ],
            200,
            $layoutPath
        );
    }
}
