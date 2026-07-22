<?php

namespace App\Reportes\Controllers;

use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/view-response.php';

class AdminListadoReportesPageController
{
    public function __construct(
        private ViewResponse $viewResponse
    ) {
    }

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->viewResponse->render(
            __DIR__ . '/../views/pages/admin-listado-reportes.page.php',
            'Reportes - Panel Admin - Flyto',
            [
                'reportes' => [
                    [
                        'titulo' => 'Reporte de ventas',
                        'descripcion' => 'Ingresos totales, comision de Flyto y top de aerolineas con mayor facturacion del periodo.',
                        'slug' => 'ventas',
                    ],
                    [
                        'titulo' => 'Reporte de vuelos',
                        'descripcion' => 'Cantidad de vuelos operados, ocupacion promedio y aerolineas mas activas en el periodo seleccionado.',
                        'slug' => 'vuelos',
                    ],
                    [
                        'titulo' => 'Reporte de CEOs',
                        'descripcion' => 'CEOs registrados, nuevos CEOs del mes, promociones activas y CEO con mayor facturacion.',
                        'slug' => 'ceos',
                    ],
                ],
            ],
            200,
            $layoutPath
        );
    }
}
