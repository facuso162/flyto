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

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->viewResponse->render(
            __DIR__ . '/../views/pages/listado-reportes.page.php',
            'Reportes - Panel CEO - Flyto',
            [
                'reportes' => [
                    [
                        'titulo' => 'Reporte de ventas',
                        'descripcion' => 'Resumen mensual de reservas, ingresos, cancelaciones y vuelos con mayor demanda.',
                        'slug' => 'ventas',
                    ],
                    [
                        'titulo' => 'Reporte de ocupacion de vuelos',
                        'descripcion' => 'Analisis mensual de vuelos, ocupacion, asientos ofrecidos y rutas con mayor demanda.',
                        'slug' => 'vuelos',
                    ],
                ],
            ],
            200,
            $layoutPath
        );
    }
}
