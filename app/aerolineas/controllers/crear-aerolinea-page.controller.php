<?php

namespace App\Aerolineas\Controllers;

use App\Paises\Services\PaisService;
use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../paises/services/pais.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class CrearAerolineaPageController
{
    public function __construct(
        private PaisService $paisService,
        private ViewResponse $viewResponse
    ) {
    }

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->viewResponse->render(
            __DIR__ . '/../views/pages/crear-aerolinea.page.php',
            'Agregar aerolinea - Panel Admin - Flyto',
            [
                'paises' => $this->paisService->getAll(),
                'flash' => Flash::consume(),
                'oldInput' => Flash::consumeOld(),
            ],
            200,
            $layoutPath
        );
    }
}
