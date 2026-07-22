<?php

namespace App\Aerolineas\Controllers;

use App\Aerolineas\Services\AerolineaService;
use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/aerolinea.service.php';

class ListadoAerolineasPageController
{
    // TODO - Todas las paginas con paginacion deberian tener una constante como esta 
    private const AEROLINEAS_POR_PAGINA = 3;

    public function __construct(
        private AerolineaService $aerolineaService,
        private ViewResponse $viewResponse
    ) {
    }

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        $aerolineas = $this->aerolineaService->getTodas();
        $total = count($aerolineas);
        $totalPaginas = max(1, (int) ceil($total / self::AEROLINEAS_POR_PAGINA));
        $pagina = min($this->paginaDesdeQuery($query), $totalPaginas);
        $offset = ($pagina - 1) * self::AEROLINEAS_POR_PAGINA;

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/listado-aerolineas.page.php',
            'Aerolineas - Panel Admin - Flyto',
            [
                'aerolineas' => array_slice(
                    $aerolineas,
                    $offset,
                    self::AEROLINEAS_POR_PAGINA
                ),
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginas,
                'totalAerolineas' => $total,
                'flash' => Flash::consume(),
            ],
            200,
            $layoutPath
        );
    }

    // TODO - Usar esta funcion en todas las paginas
    private function paginaDesdeQuery(array $query): int
    {
        $valor = $query['pagina'] ?? 1;
        $pagina = is_scalar($valor) ? filter_var($valor, FILTER_VALIDATE_INT) : false;

        return $pagina !== false && $pagina > 0 ? $pagina : 1;
    }
}
