<?php

namespace App\Promociones\Controllers;

use App\Auth\Middlewares\CeoMiddleware;
use App\Auth\Services\SessionService;
use App\Promociones\Dtos\EditarPromocionDto;
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
require_once __DIR__ . '/../dtos/editar-promocion.dto.php';
require_once __DIR__ . '/../services/promocion.service.php';
require_once __DIR__ . '/../validators/promocion.validator.php';

class EditarPromocionActionController
{
    public function __construct(
        private PromocionService $promocionService,
        private SessionService $sessionService
    ) {
    }

    public function editar(array $params = [], array $query = []): void
    {
        try {
            $middleware = new CeoMiddleware($this->sessionService);
            $middleware->handle();
        } catch (HttpException $exception) {
            Flash::error('Necesitás permisos de CEO para crear un vuelo.');
            RedirectResponse::to($exception->getStatusCode() === 401 ? '/auth/login' : '/', [], 303);
            return;
        }

        try {
            PromocionValidator::editar($_POST);
            $dto = new EditarPromocionDto(
                id: (int) $_POST['id'],
                descripcion: trim((string) $_POST['descripcion']),
                descuento: (int) $_POST['descuento']
            );
            $usuario = $this->sessionService->getUser() ?? [];

            $this->promocionService->editar($dto, (int) ($usuario['id'] ?? 0));

            Flash::success('Promoción editada correctamente.');
            RedirectResponse::to('/ceo/promociones', [], 303);
        } catch (HttpException $exception) {
            $field = $exception->getDetails()['field'] ?? null;

            if (in_array($field, ['descripcion', 'descuento'], true)) {
                Flash::error('No pudimos editar la promoción. Revisá los datos e intentá nuevamente.');
                Flash::validationErrors($this->validationErrorsFromException($exception));
                Flash::old($this->safeOldInput($_POST));
                RedirectResponse::to($this->editUrl($_POST), [], 303);
            } else {
                Flash::error($exception->getMessage());
                RedirectResponse::to('/ceo/promociones', [], 303);
            }
        } catch (Throwable) {
            Flash::error('No pudimos editar la promoción. Intentá nuevamente en unos minutos.');
            Flash::old($this->safeOldInput($_POST));
            RedirectResponse::to($this->editUrl($_POST), [], 303);
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

    private function editUrl(array $data): string
    {
        $id = $data['id'] ?? null;
        $id = is_scalar($id) ? filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;

        return $id === false
            ? '/ceo/promociones'
            : '/ceo/promociones/editar?' . http_build_query(['id' => $id]);
    }
}
