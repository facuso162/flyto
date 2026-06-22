<?php

namespace App\Promociones\Controllers;

use App\Auth\Middlewares\CeoMiddleware;
use App\Auth\Services\SessionService;
use App\Promociones\Dtos\CrearPromocionDto;
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
require_once __DIR__ . '/../dtos/crear-promocion.dto.php';
require_once __DIR__ . '/../services/promocion.service.php';
require_once __DIR__ . '/../validators/promocion.validator.php';

class CrearPromocionActionController
{
    public function __construct(
        private PromocionService $promocionService,
        private SessionService $sessionService
    ) {
    }

    public function crear(array $params = [], array $query = []): void
    {
        try {
            $middleware = new CeoMiddleware($this->sessionService);
            $middleware->handle();
        } catch (HttpException $exception) {
            Flash::error('Necesitás permisos de CEO para crear una promocion.');
            RedirectResponse::to($exception->getStatusCode() === 401 ? '/auth/login' : '/', [], 303);
            return;
        }

        try {
            PromocionValidator::crear($_POST);
            $dto = new CrearPromocionDto(
                descripcion: trim((string) $_POST['descripcion']),
                descuento: (int) $_POST['descuento']
            );
            $usuario = $this->sessionService->getUser() ?? [];

            $this->promocionService->crear($dto, (int) ($usuario['id'] ?? 0));

            Flash::success('Promocion creada correctamente. Queda inactiva hasta solicitar su activacion.');
            RedirectResponse::to('/ceo/promociones', [], 303);
        } catch (HttpException $exception) {
            Flash::error('No pudimos crear la promocion. Revisa los datos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($this->safeOldInput($_POST));
            RedirectResponse::to('/ceo/promociones/crear', [], 303);
        } catch (Throwable) {
            Flash::error('No pudimos crear la promocion. Intentalo nuevamente en unos minutos.');
            Flash::old($this->safeOldInput($_POST));
            RedirectResponse::to('/ceo/promociones/crear', [], 303);
        }
    }

    private function validationErrorsFromException(HttpException $exception): array
    {
        $field = $exception->getDetails()['field'] ?? null;
        return is_string($field) && $field !== ''
            ? [$field => $exception->getMessage()]
            : ['general' => $exception->getMessage()];
    }

    private function safeOldInput(array $data): array
    {
        $oldInput = [];
        foreach (['descripcion', 'descuento'] as $field) {
            if (isset($data[$field]) && is_scalar($data[$field])) {
                $oldInput[$field] = (string) $data[$field];
            }
        }
        return $oldInput;
    }
}
