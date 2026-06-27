<?php

namespace App\Novedades\Controllers;

use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class CrearNovedadPageController
{
    public function __construct(private ViewResponse $viewResponse)
    {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->viewResponse->render(
            __DIR__ . '/../views/pages/crear-novedad.page.php',
            'Crear novedad - Panel Admin - Flyto',
            ['flash' => Flash::consume(), 'oldInput' => Flash::consumeOld()],
            200,
            $layoutPath
        );
    }
}
