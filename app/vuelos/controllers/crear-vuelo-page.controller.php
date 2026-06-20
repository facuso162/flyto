<?php

namespace App\Vuelos\Controllers;

use App\Auth\Services\SessionService;
use App\Ciudades\Services\CiudadService;
use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;
use App\Vuelos\Services\VueloService;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../ciudades/services/ciudad.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/vuelo.service.php';

class CrearVueloPageController
{
    public function __construct(
        private CiudadService $ciudadService,
        private VueloService $vueloService,
        private SessionService $sessionService,
        private ViewResponse $viewResponse
    ) {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $usuario = $this->sessionService->getUser() ?? [];
        $ceoId = (int) ($usuario['id'] ?? 0);
        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/crear-vuelo.page.php',
            'Crear vuelo - Panel CEO - Flyto',
            [
                'ciudades' => $this->ciudadService->getTodas(),
                'codigoVueloPropuesto' => $this->vueloService->proponerCodigoByCeoId($ceoId),
                'flash' => $flash,
                'oldInput' => $oldInput,
            ],
            200,
            $layoutPath
        );
    }
}
