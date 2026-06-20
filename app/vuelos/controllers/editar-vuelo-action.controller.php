<?php

namespace App\Vuelos\Controllers;

use App\Auth\Middlewares\CeoMiddleware;
use App\Auth\Services\SessionService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Vuelos\Dtos\EditarVueloDto;
use App\Vuelos\Services\VueloService;
use App\Vuelos\Validators\EditarVueloValidator;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/ceo.middleware.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../dtos/editar-vuelo.dto.php';
require_once __DIR__ . '/../services/vuelo.service.php';
require_once __DIR__ . '/../validators/editar-vuelo.validator.php';

class EditarVueloActionController
{
    public function __construct(
        private VueloService $vueloService,
        private SessionService $sessionService
    ) {
    }

    public function editar(array $params = [], array $query = []): void
    {
        try {
            (new CeoMiddleware($this->sessionService))->handle();
        } catch (HttpException $exception) {
            Flash::error('Necesitás permisos de CEO para editar un vuelo.');
            RedirectResponse::to($exception->getStatusCode() === 401 ? '/auth/login' : '/', [], 303);
            return;
        }

        $vueloId = $this->vueloId($_POST);
        $editPath = $vueloId > 0 ? '/ceo/vuelos/editar?id=' . $vueloId : '/ceo/vuelos';

        try {
            EditarVueloValidator::validate($_POST);
            $usuario = $this->sessionService->getUser() ?? [];
            $this->vueloService->editar($vueloId, $this->dtoFromPost($_POST), (int) ($usuario['id'] ?? 0));

            Flash::success('Vuelo editado correctamente.');
            RedirectResponse::to('/ceo/vuelos', [], 303);
        } catch (HttpException $exception) {
            Flash::error('No pudimos editar el vuelo. Revisá los datos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($this->safeOldInput($_POST));
            RedirectResponse::to($editPath, [], 303);
        } catch (Throwable) {
            Flash::error('No pudimos editar el vuelo. Intentalo nuevamente en unos minutos.');
            Flash::old($this->safeOldInput($_POST));
            RedirectResponse::to($editPath, [], 303);
        }
    }

    private function vueloId(array $data): int
    {
        $value = $data['vueloId'] ?? null;
        $id = is_scalar($value)
            ? filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;

        return $id === false ? 0 : (int) $id;
    }

    private function dtoFromPost(array $data): EditarVueloDto
    {
        return new EditarVueloDto(
            codigoVuelo: strtoupper(trim((string) $data['codigoVuelo'])),
            precio: (float) $data['precio'],
            asientosDisponibles: (int) $data['asientosDisponibles'],
            fechaSalida: new \DateTimeImmutable((string) $data['fechaSalida']),
            fechaLlegada: new \DateTimeImmutable((string) $data['fechaLlegada']),
            origenCiudadId: (int) $data['origenCiudadId'],
            destinoCiudadId: (int) $data['destinoCiudadId'],
            duracionHoras: (float) $data['duracionHoras'],
            distanciaKm: (int) $data['distanciaKm']
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
        $fields = [
            'codigoVuelo', 'precio', 'asientosDisponibles', 'fechaSalida', 'fechaLlegada',
            'origenCiudadId', 'destinoCiudadId', 'duracionHoras', 'distanciaKm',
        ];
        $oldInput = [];

        foreach ($fields as $field) {
            if (isset($data[$field]) && is_scalar($data[$field])) {
                $oldInput[$field] = (string) $data[$field];
            }
        }

        return $oldInput;
    }
}
