<?php

namespace App\Promociones\Controllers;

use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class CrearPromocionPageController
{
    public function __construct(private ViewResponse $viewResponse)
    {
    }

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->viewResponse->render(
            __DIR__ . '/../views/pages/crear-promocion.page.php',
            'Crear promocion - Panel CEO - Flyto',
            ['flash' => Flash::consume(), 'oldInput' => Flash::consumeOld()],
            200,
            $layoutPath
        );
    }
}
