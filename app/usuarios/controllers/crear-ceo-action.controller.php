<?php

namespace App\Usuarios\Controllers;

use App\Auth\Middlewares\AdminMiddleware;
use App\Auth\Services\SessionService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Usuarios\Dtos\CrearCeoDto;
use App\Usuarios\Services\UsuarioService;
use App\Usuarios\Validators\CrearCeoValidator;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/admin.middleware.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../dtos/crear-ceo.dto.php';
require_once __DIR__ . '/../services/usuario.service.php';
require_once __DIR__ . '/../validators/crear-ceo.validator.php';

class CrearCeoActionController
{
    public function __construct(
        private UsuarioService $usuarioService,
        private SessionService $sessionService
    ) {
    }

    public function crear(array $params = [], array $query = []): void
    {
        try {
            $middleware = new AdminMiddleware($this->sessionService);
            $middleware->handle();
        } catch (HttpException $exception) {
            Flash::error('Necesitas permisos de administrador para crear un CEO.');
            RedirectResponse::to($exception->getStatusCode() === 401 ? '/auth/login' : '/', [], 303);
            return;
        }

        try {
            CrearCeoValidator::validate($_POST);
            $this->usuarioService->crearCeo($this->dtoFromPost($_POST));

            Flash::success('CEO creado correctamente.');
            RedirectResponse::to('/admin/ceos', [], 303);
        } catch (HttpException $exception) {
            Flash::error('No pudimos crear el CEO. Revisa los datos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($this->safeOldInput($_POST));
            RedirectResponse::to('/admin/ceos/crear', [], 303);
        } catch (Throwable) {
            Flash::error('No pudimos crear el CEO. Intentalo nuevamente en unos minutos.');
            Flash::old($this->safeOldInput($_POST));
            RedirectResponse::to('/admin/ceos/crear', [], 303);
        }
    }

    private function dtoFromPost(array $data): CrearCeoDto
    {
        return new CrearCeoDto(
            nombre: trim((string) $data['nombre']),
            apellido: trim((string) $data['apellido']),
            email: strtolower(trim((string) $data['email'])),
            password: (string) $data['password'],
            aerolineaId: (int) $data['aerolineaId']
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
        foreach (['nombre', 'apellido', 'email', 'aerolineaId'] as $field) {
            if (isset($data[$field]) && is_scalar($data[$field])) {
                $oldInput[$field] = (string) $data[$field];
            }
        }

        return $oldInput;
    }
}
