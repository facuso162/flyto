<?php

namespace App\Promociones\Controllers;

use App\Auth\Middlewares\CeoMiddleware;
use App\Auth\Services\SessionService;
use App\Promociones\Dtos\ActivarPromocionDto;
use App\Promociones\Services\PromocionService;
use App\Promociones\Validators\PromocionValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/ceo.middleware.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../dtos/activar-promocion.dto.php';
require_once __DIR__ . '/../services/promocion.service.php';
require_once __DIR__ . '/../validators/promocion.validator.php';

class SolicitarActivacionActionController
{
    public function __construct(
        private PromocionService $promocionService,
        private SessionService $sessionService
    ) {
    }

    public function solicitar(array $params = [], array $query = []): void
    {
        try {
            $middleware = new CeoMiddleware($this->sessionService);
            $middleware->handle();
        } catch (HttpException $exception) {
            Flash::error('Necesitás permisos de CEO para solicitar la activación de una promoción.');
            RedirectResponse::to($exception->getStatusCode() === 401 ? '/auth/login' : '/', [], 303);
            return;
        }

        try {
            PromocionValidator::activar($_POST);

            $dto = new ActivarPromocionDto(
                id: (int) $_POST['id'],
                fechaFin: \DateTime::createFromFormat('!Y-m-d', trim((string) $_POST['fecha_fin']))
            );

            $this->promocionService->solicitarActivacion($dto);
            Flash::success('Solicitud de activación realizada correctamente.');
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
        } catch (Throwable) {
            Flash::error('No pudimos realizar la solicitud de activación. Intentá nuevamente en unos minutos.');
        }

        RedirectResponse::to('/ceo/promociones', [], 303);
    }
}
