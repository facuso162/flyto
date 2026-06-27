<?php

namespace App\Novedades\Controllers;

use App\Auth\Middlewares\AdminMiddleware;
use App\Auth\Services\SessionService;
use App\Novedades\Dtos\CrearNovedadDto;
use App\Novedades\Services\NovedadService;
use App\Novedades\Validators\NovedadValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/admin.middleware.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../dtos/crear-novedad.dto.php';
require_once __DIR__ . '/../services/novedad.service.php';
require_once __DIR__ . '/../validators/novedad.validator.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class CrearNovedadActionController
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

    public function crear(array $params = [], array $query = []): void
    {
        if (!$this->ensureAdmin()) {
            return;
        }

        try {
            NovedadValidator::crear($_POST);
            $dto = new CrearNovedadDto(
                titulo: $this->stringInput($_POST, 'titulo'),
                texto: $this->stringInput($_POST, 'texto'),
                categoria: $this->stringInput($_POST, 'categoria'),
                fechaExpiracion: $this->fechaExpiracion($_POST)
            );

            $this->novedadService->crear($dto);

            Flash::success('Novedad creada correctamente.');
            RedirectResponse::to('/admin/novedades', [], 303);
        } catch (HttpException $exception) {
            Flash::error('No pudimos crear la novedad. Revisa los datos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($this->safeOldInput($_POST));

            RedirectResponse::to('/admin/novedades/crear', [], 303);
        } catch (Throwable) {
            Flash::error('No pudimos crear la novedad. Intentalo nuevamente en unos minutos.');
            Flash::old($this->safeOldInput($_POST));

            RedirectResponse::to('/admin/novedades/crear', [], 303);
        }
    }

    private function ensureAdmin(): bool
    {
        try {
            $middleware = new AdminMiddleware($this->sessionService);
            $middleware->handle();

            return true;
        } catch (HttpException $exception) {
            Flash::error('Necesitas permisos de administrador para realizar esta accion.');

            if ($exception->getStatusCode() === 401) {
                RedirectResponse::to('/auth/login', [], 303);
                return false;
            }

            RedirectResponse::to('/', [], 303);
            return false;
        }
    }

    private function validationErrorsFromException(HttpException $exception): array
    {
        $details = $exception->getDetails();
        $field = $details['field'] ?? null;

        if (!is_string($field) || $field === '') {
            return ['general' => $exception->getMessage()];
        }

        return [$field => $exception->getMessage()];
    }

    private function safeOldInput(array $data): array
    {
        $oldInput = [];

        foreach (['titulo', 'categoria', 'texto', 'fechaExpiracion'] as $field) {
            if (isset($data[$field]) && is_scalar($data[$field])) {
                $oldInput[$field] = (string) $data[$field];
            }
        }

        return $oldInput;
    }

    private function stringInput(array $data, string $field): string
    {
        return isset($data[$field]) && is_scalar($data[$field])
            ? trim((string) $data[$field])
            : '';
    }

    private function fechaExpiracion(array $data): \DateTime
    {
        return \DateTime::createFromFormat('!Y-m-d', $this->stringInput($data, 'fechaExpiracion')) ?: new \DateTime();
    }
}
