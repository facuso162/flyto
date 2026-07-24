<?php

namespace App\Shared\Controllers;

use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../http/view-response.php';

class NotFoundPageController
{
    public function __construct(private ViewResponse $viewResponse)
    {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->viewResponse->render(
            __DIR__ . '/../views/pages/not-found.page.php',
            'Página no encontrada - Flyto',
            [],
            404,
            $layoutPath
        );
    }
}
