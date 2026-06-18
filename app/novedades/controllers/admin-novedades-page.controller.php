<?php

namespace App\Novedades\Controllers;

use App\Auth\Middlewares\AdminMiddleware;
use App\Auth\Services\SessionService;
use App\Novedades\Services\NovedadService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/admin.middleware.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../services/novedad.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class AdminNovedadesPageController
{
    private NovedadService $novedadService;
    private SessionService $sessionService;

    public function __construct(
        NovedadService $novedadService,
        SessionService $sessionService
    ) {
        $this->novedadService = $novedadService;
        $this->sessionService = $sessionService;
    }

    public function show(array $params = [], array $query = []): void
    {
        if (!$this->ensureAdmin()) {
            return;
        }

        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();
        $validationErrors = $flash['validationErrors'] ?? [];

        try {
            $novedades = array_map(
                fn ($novedad) => $novedad->toArray(),
                $this->novedadService->getTodas()
            );
        } catch (Throwable) {
            $novedades = [];
            $flash['error'] = 'No pudimos cargar las novedades. Intentalo nuevamente en unos minutos.';
        }

        ViewResponse::render(
            __DIR__ . '/../views/pages/admin-novedades.page.php',
            'Administrar novedades - Flyto',
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

    private function ensureAdmin(): bool
    {
        try {
            $middleware = new AdminMiddleware($this->sessionService);
            $middleware->handle();

            return true;
        } catch (HttpException $exception) {
            Flash::error('Necesitas permisos de administrador para acceder a esta pagina.');

            if ($exception->getStatusCode() === 401) {
                RedirectResponse::to('/login', [], 303);
                return false;
            }

            RedirectResponse::to('/', [], 303);
            return false;
        }
    }
}
