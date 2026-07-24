<?php

namespace App\Vuelos\Controllers;

use App\Auth\Middlewares\CeoMiddleware;
use App\Auth\Services\SessionService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Vuelos\Dtos\CrearVueloDto;
use App\Vuelos\Services\VueloService;
use App\Vuelos\Validators\CrearVueloValidator;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/ceo.middleware.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../dtos/crear-vuelo.dto.php';
require_once __DIR__ . '/../services/vuelo.service.php';
require_once __DIR__ . '/../validators/crear-vuelo.validator.php';

class CrearVueloActionController
{
    public function __construct(
        private VueloService $vueloService,
        private SessionService $sessionService
    ) {
    }

    public function crear(array $params = [], array $query = []): void
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
            CrearVueloValidator::validate($_POST);
            $dto = $this->dtoFromPost($_POST);
            $usuario = $this->sessionService->getUser() ?? [];

            $this->vueloService->crear($dto, (int) ($usuario['id'] ?? 0));

            Flash::success('Vuelo creado correctamente.');
            RedirectResponse::to('/ceo/vuelos', [], 303);
        } catch (HttpException $exception) {
            Flash::error('No pudimos crear el vuelo. Revisá los datos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($this->safeOldInput($_POST));
            RedirectResponse::to('/ceo/vuelos/crear', [], 303);
        } catch (Throwable) {
            Flash::error('No pudimos crear el vuelo. Intentalo nuevamente en unos minutos.');
            Flash::old($this->safeOldInput($_POST));
            RedirectResponse::to('/ceo/vuelos/crear', [], 303);
        }
    }

    private function dtoFromPost(array $data): CrearVueloDto
    {
        return new CrearVueloDto(
            codigoVuelo: strtoupper(trim((string) $data['codigoVuelo'])),
            precio: (float) $data['precio'],
            asientosDisponibles: (int) $data['asientosDisponibles'],
            fechaSalida: new \DateTimeImmutable((string) $data['fechaSalida']),
            fechaLlegada: new \DateTimeImmutable((string) $data['fechaLlegada']),
            origenCiudadId: (int) $data['origenCiudadId'],
            destinoCiudadId: (int) $data['destinoCiudadId'],
            duracionHoras: $this->durationHours($data),
            distanciaKm: (int) $data['distanciaKm']
        );
    }

    private function durationHours(array $data): float
    {
        $salida = new \DateTimeImmutable((string) $data['fechaSalida']);
        $llegada = new \DateTimeImmutable((string) $data['fechaLlegada']);
        return round(($llegada->getTimestamp() - $salida->getTimestamp()) / 3600, 2);
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
            'codigoVuelo',
            'precio',
            'asientosDisponibles',
            'fechaSalida',
            'fechaLlegada',
            'origenCiudadId',
            'destinoCiudadId',
            'duracionHoras',
            'distanciaKm',
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
