<?php

namespace App\Vuelos\Controllers;

use App\Auth\Services\SessionService;
use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;
use App\Vuelos\Services\VueloService;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/vuelo.service.php';

class ListadoVuelosPageController
{
    private const ESTADOS = ['completado', 'pendiente', 'cancelado'];
    private const VUELOS_POR_PAGINA = 3;

    public function __construct(
        private VueloService $vueloService,
        private SessionService $sessionService,
        private ViewResponse $viewResponse
    ) {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $usuario = $this->sessionService->getUser() ?? [];
        $ceoId = (int) ($usuario['id'] ?? 0);
        $estado = $this->estadoDesdeQuery($query);
        $pagina = $this->paginaDesdeQuery($query);
        $listado = $this->vueloService->getPaginatedByCeoId(
            $ceoId,
            $estado,
            $pagina,
            self::VUELOS_POR_PAGINA
        );
        $flash = Flash::consume();

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/listado-vuelos.page.php',
            'Vuelos - Panel CEO - Flyto',
            [
                'vuelos' => $listado['vuelos'],
                'estadoSeleccionado' => $estado,
                'paginaActual' => $listado['pagina'],
                'totalPaginas' => $listado['totalPaginas'],
                'totalVuelos' => $listado['total'],
                'flash' => $flash,
            ],
            200,
            $layoutPath
        );
    }

    private function estadoDesdeQuery(array $query): ?string
    {
        $valor = $query['estado'] ?? '';
        $estado = is_scalar($valor) ? strtolower(trim((string) $valor)) : '';

        return in_array($estado, self::ESTADOS, true) ? $estado : null;
    }

    private function paginaDesdeQuery(array $query): int
    {
        $valor = $query['pagina'] ?? 1;
        $pagina = is_scalar($valor) ? filter_var($valor, FILTER_VALIDATE_INT) : false;

        return $pagina !== false && $pagina > 0 ? $pagina : 1;
    }
}
