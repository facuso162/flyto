<?php

namespace App\Novedades\Controllers;

use App\Novedades\Services\NovedadService;
use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;
use Throwable;

require_once __DIR__ . '/../services/novedad.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class AdminNovedadesPageController
{
    private NovedadService $novedadService;
    private ViewResponse $viewResponse;

    public function __construct(NovedadService $novedadService, ViewResponse $viewResponse)
    {
        $this->novedadService = $novedadService;
        $this->viewResponse = $viewResponse;
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $flash = Flash::consume();
        $itemsPorPagina = 3;
        $paginaActual = max(1, (int) ($query['pagina'] ?? 1));

        try {
            $novedades = array_map(
                fn ($novedad) => $novedad->toArray(),
                $this->novedadService->getTodas()
            );
        } catch (Throwable) {
            $novedades = [];
            $flash['error'] = 'No pudimos cargar las novedades. Intentalo nuevamente en unos minutos.';
        }

        $totalNovedades = count($novedades);
        $totalPaginas = max(1, (int) ceil($totalNovedades / $itemsPorPagina));
        $paginaActual = min($paginaActual, $totalPaginas);
        $novedadesPagina = array_slice($novedades, ($paginaActual - 1) * $itemsPorPagina, $itemsPorPagina);

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/admin-novedades.page.php',
            'Administrar novedades - Flyto',
            [
                'novedades' => $novedadesPagina,
                'paginaActual' => $paginaActual,
                'totalPaginas' => $totalPaginas,
                'totalNovedades' => $totalNovedades,
                'flash' => [
                    'success' => $flash['success'] ?? null,
                    'error' => $flash['error'] ?? null,
                ],
            ],
            200,
            $layoutPath
        );
    }
}
