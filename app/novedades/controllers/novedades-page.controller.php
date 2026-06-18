<?php

namespace App\Novedades\Controllers;

use App\Novedades\Services\NovedadService;
use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;
use Throwable;

require_once __DIR__ . '/../services/novedad.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class NovedadesPageController
{
    private NovedadService $novedadService;

    public function __construct(NovedadService $novedadService)
    {
        $this->novedadService = $novedadService;
    }

    public function show(array $params = [], array $query = []): void
    {
        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();
        $validationErrors = $flash['validationErrors'] ?? [];

        try {
            $novedades = array_map(
                fn ($novedad) => $novedad->toArray(),
                $this->novedadService->getVigentes()
            );
        } catch (Throwable) {
            $novedades = [];
            $flash['error'] = 'No pudimos cargar las novedades. Intentalo nuevamente en unos minutos.';
        }

        ViewResponse::render(
            __DIR__ . '/../views/pages/novedades.page.php',
            'Novedades - Flyto',
            [
                'novedades' => $novedades,
                'flash' => [
                    'success' => $flash['success'] ?? null,
                    'error' => $flash['error'] ?? null,
                ],
                'oldInput' => is_array($oldInput) ? $oldInput : [],
                'validationErrors' => is_array($validationErrors) ? $validationErrors : [],
            ]
        );
    }
}
