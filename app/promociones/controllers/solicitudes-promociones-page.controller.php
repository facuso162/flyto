<?php

namespace App\Promociones\Controllers;

use App\Promociones\Services\PromocionService;
use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/promocion.service.php';

class SolicitudesPromocionesPageController
{
    private const ESTADO_PENDIENTE = 'pendiente_activacion'; // TODO - las constantes de tipos deberian estar en un enum
    private const SOLICITUDES_POR_PAGINA = 3;

    public function __construct(
        private PromocionService $promocionService,
        private ViewResponse $viewResponse
    ) {
    }

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        $solicitudes = $this->promocionService->getByEstado(self::ESTADO_PENDIENTE);
        $total = count($solicitudes);
        $totalPaginas = max(1, (int) ceil($total / self::SOLICITUDES_POR_PAGINA));
        $pagina = min($this->paginaDesdeQuery($query), $totalPaginas);
        $offset = ($pagina - 1) * self::SOLICITUDES_POR_PAGINA;

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/solicitudes-promociones.page.php',
            'Solicitudes de promociones - Panel Admin - Flyto',
            [
                'solicitudes' => array_slice($solicitudes, $offset, self::SOLICITUDES_POR_PAGINA),
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginas,
                'totalSolicitudes' => $total,
                'flash' => Flash::consume(),
            ],
            200,
            $layoutPath
        );
    }

    private function paginaDesdeQuery(array $query): int
    {
        $valor = $query['pagina'] ?? 1;
        $pagina = is_scalar($valor) ? filter_var($valor, FILTER_VALIDATE_INT) : false;

        return $pagina !== false && $pagina > 0 ? $pagina : 1;
    }
}
