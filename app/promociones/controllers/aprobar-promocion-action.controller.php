<?php

namespace App\Promociones\Controllers;

use App\Auth\Middlewares\AdminMiddleware;
use App\Auth\Services\SessionService;
use App\Promociones\Services\PromocionService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/admin.middleware.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../services/promocion.service.php';

class AprobarPromocionActionController
{
    public function __construct(
        private PromocionService $promocionService,
        private SessionService $sessionService
    ) {
    }

    public function aprobar(array $params = [], array $query = []): void
    {
        if (!$this->ensureAdmin()) {
            return;
        }

        try {
            $valor = $_POST['id'] ?? null;
            $promocionId = is_scalar($valor) ? (int) $valor : 0;

            $this->promocionService->aprobar($promocionId);
            Flash::success('Solicitud de promoción aprobada correctamente.');
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
        } catch (Throwable) {
            Flash::error('No pudimos aprobar la solicitud. Intentá nuevamente en unos minutos.');
        }

        RedirectResponse::to('/admin/promociones', [], 303);
    }

    private function ensureAdmin(): bool
    {
        try {
            $middleware = new AdminMiddleware($this->sessionService);
            $middleware->handle();

            return true;
        } catch (HttpException $exception) {
            Flash::error('Necesitás permisos de administrador para realizar esta acción.');
            RedirectResponse::to($exception->getStatusCode() === 401 ? '/auth/login' : '/', [], 303);

            return false;
        }
    }
}
