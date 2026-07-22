<?php

namespace App\Perfil\Controllers;

use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class MiPerfilPageController
{
    private ViewResponse $viewResponse;

    public function __construct(ViewResponse $viewResponse)
    {
        $this->viewResponse = $viewResponse;
    }

    // TODO - Dejar de usar el array $params y el array $query
    // TODO - Usar el parametro de layoutPath como se hace en todas las paginas
    public function show(array $params = [], array $query = [], ?string $layoutPath = null): void
    {
        $flash = Flash::consume();

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/mi-perfil.page.php',
            'Mi perfil - Flyto',
            ['reservaFeedback' => $flash['success'] ?? $flash['error'] ?? null],
            200,
            // TODO - Usar el parametro de layoutPath como se hace en todas las paginas
            $layoutPath ?? __DIR__ . '/../../shared/views/layouts/public.layout.php'
        );
    }
}
