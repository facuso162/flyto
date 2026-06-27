<?php

namespace App\Aerolineas\Controllers;

use App\Aerolineas\Dtos\CrearAerolineaDto;
use App\Aerolineas\Services\AerolineaService;
use App\Aerolineas\Validators\CrearAerolineaValidator;
use App\Auth\Middlewares\AdminMiddleware;
use App\Auth\Services\SessionService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/admin.middleware.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../dtos/crear-aerolinea.dto.php';
require_once __DIR__ . '/../services/aerolinea.service.php';
require_once __DIR__ . '/../validators/crear-aerolinea.validator.php';

class CrearAerolineaActionController
{
    public function __construct(
        private AerolineaService $aerolineaService,
        private SessionService $sessionService
    ) {
    }

    public function crear(array $params = [], array $query = []): void
    {
        try {
            $middleware = new AdminMiddleware($this->sessionService);
            $middleware->handle();
        } catch (HttpException $exception) {
            Flash::error('Necesitas permisos de administrador para crear una aerolinea.');
            RedirectResponse::to($exception->getStatusCode() === 401 ? '/auth/login' : '/', [], 303);
            return;
        }

        try {
            CrearAerolineaValidator::validate($_POST);
            $this->aerolineaService->crear($this->dtoFromPost($_POST));

            Flash::success('Aerolinea creada correctamente.');
            RedirectResponse::to('/admin/aerolineas', [], 303);
        } catch (HttpException $exception) {
            Flash::error('No pudimos crear la aerolinea. Revisa los datos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($this->safeOldInput($_POST));
            RedirectResponse::to('/admin/aerolineas/crear', [], 303);
        } catch (Throwable) {
            Flash::error('No pudimos crear la aerolinea. Intentalo nuevamente en unos minutos.');
            Flash::old($this->safeOldInput($_POST));
            RedirectResponse::to('/admin/aerolineas/crear', [], 303);
        }
    }

    private function dtoFromPost(array $data): CrearAerolineaDto
    {
        return new CrearAerolineaDto(
            nombre: trim((string) $data['nombre']),
            codigoIata: strtoupper(trim((string) $data['codigoIata'])),
            descripcion: trim((string) $data['descripcion']),
            paisId: (int) $data['paisId']
        );
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
        foreach (['nombre', 'codigoIata', 'descripcion', 'paisId'] as $field) {
            if (isset($data[$field]) && is_scalar($data[$field])) {
                $oldInput[$field] = (string) $data[$field];
            }
        }

        return $oldInput;
    }
}
