<?php

namespace App\Promociones\Controllers;

use App\Auth\Services\SessionService;
use App\Promociones\Models\Promocion;
use App\Promociones\Services\PromocionService;
use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/promocion.service.php';

class ListadoPromocionesPageController
{
    private const PROMOCIONES_POR_PAGINA = 3;

    public function __construct(
        private PromocionService $promocionService,
        private SessionService $sessionService,
        private ViewResponse $viewResponse
    ) {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $usuario = $this->sessionService->getUser() ?? [];
        $ceoId = (int) ($usuario['id'] ?? 0);
        $promociones = $this->promocionService->getByCeoId($ceoId);

        $promocionesActivas = array_values(array_filter(
            $promociones,
            fn (Promocion $promocion): bool => $this->esActiva($promocion)
        ));
        $promocionesInactivas = array_values(array_filter(
            $promociones,
            fn (Promocion $promocion): bool => !$this->esActiva($promocion)
        ));
        $promocionesOrdenadas = array_merge($promocionesActivas, $promocionesInactivas);

        $total = count($promocionesOrdenadas);
        $totalPaginas = max(1, (int) ceil($total / self::PROMOCIONES_POR_PAGINA));
        $pagina = min($this->paginaDesdeQuery($query), $totalPaginas);
        $offset = ($pagina - 1) * self::PROMOCIONES_POR_PAGINA;

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/listado-promociones.page.php',
            'Promociones - Panel CEO - Flyto',
            [
                'promociones' => array_slice(
                    $promocionesOrdenadas,
                    $offset,
                    self::PROMOCIONES_POR_PAGINA
                ),
                'tienePromocionActiva' => $promocionesActivas !== [],
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginas,
                'totalPromociones' => $total,
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

    private function esActiva(Promocion $promocion): bool
    {
        return $promocion->activa
            && strtolower((string) ($promocion->estado['descripcion'] ?? '')) === 'activa';
    }
}
