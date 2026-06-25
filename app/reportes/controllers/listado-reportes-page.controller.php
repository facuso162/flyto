<?php

namespace App\Reportes\Controllers;

use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/view-response.php';

class ListadoReportesPageController
{
    public function __construct(
        private ViewResponse $viewResponse
    ) {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->viewResponse->render(
            __DIR__ . '/../views/pages/listado-reportes.page.php',
            'Reportes - Panel CEO - Flyto',
            [
                'reportes' => [
                    [
                        'titulo' => 'Reporte de vuelos',
                        'descripcion' => 'Analisis de vuelos, ocupacion, rutas y disponibilidad operativa de la aerolinea.',
                        'slug' => 'vuelos',
                    ],
                    [
                        'titulo' => 'Reporte de usuarios',
                        'descripcion' => 'Resumen de usuarios registrados, actividad y crecimiento de la plataforma.',
                        'slug' => 'usuarios',
                    ],
                ],
            ],
            200,
            $layoutPath
        );
    }
}
