<?php

namespace App\Ceo\Controllers;

use App\Auth\Services\SessionService;
use App\Promociones\Services\PromocionService;
use App\Shared\Http\ViewResponse;
use App\Vuelos\Services\VueloService;

class CeoDashboardPageController
{
    public function __construct(
        private VueloService $vueloService,
        private PromocionService $promocionService,
        private SessionService $sessionService,
        private ViewResponse $viewResponse
    ) {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $usuario = $this->sessionService->getUser() ?? [];
        $ceoId = (int) ($usuario['id'] ?? 0);

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/ceo.page.php',
            'Panel CEO - Flyto',
            [
                'proximosVuelos' => $this->vueloService->getProximosByCeoId($ceoId),
                'promocionActiva' => $this->promocionService->getActivaByCeoId($ceoId),
            ],
            200,
            $layoutPath
        );
    }
}
