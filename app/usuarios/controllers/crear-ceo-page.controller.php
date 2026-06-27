<?php

namespace App\Usuarios\Controllers;

use App\Aerolineas\Services\AerolineaService;
use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../aerolineas/services/aerolinea.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class CrearCeoPageController
{
    public function __construct(
        private AerolineaService $aerolineaService,
        private ViewResponse $viewResponse
    ) {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->viewResponse->render(
            __DIR__ . '/../views/pages/crear-ceo.page.php',
            'Agregar CEO - Panel Admin - Flyto',
            [
                'aerolineas' => $this->aerolineaService->getSinCeo(),
                'flash' => Flash::consume(),
                'oldInput' => Flash::consumeOld(),
            ],
            200,
            $layoutPath
        );
    }
}
