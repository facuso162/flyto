<?php

namespace App\Promociones\Controllers;

use App\Auth\Middlewares\CeoMiddleware;
use App\Auth\Services\SessionService;
use App\Promociones\Services\PromocionService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/ceo.middleware.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../services/promocion.service.php';

class DesactivarPromocionActionController
{
    public function __construct(
        private PromocionService $promocionService,
        private SessionService $sessionService
    ) {
    }

    public function desactivar(array $params = [], array $query = []): void
    {
        try {
            $middleware = new CeoMiddleware($this->sessionService);
            $middleware->handle();
        } catch (HttpException $exception) {
            Flash::error('Necesitás permisos de CEO para desactivar una promoción.');
            RedirectResponse::to($exception->getStatusCode() === 401 ? '/auth/login' : '/', [], 303);
            return;
        }

        try {
            $valor = $_POST['id'] ?? null;
            $promocionId = is_scalar($valor) ? (int) $valor : 0;

            $this->promocionService->desactivar($promocionId);
            Flash::success('Promoción desactivada correctamente.');
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
        } catch (Throwable) {
            Flash::error('No pudimos desactivar la promoción. Intentá nuevamente en unos minutos.');
        }

        RedirectResponse::to('/ceo/promociones', [], 303);
    }
}
