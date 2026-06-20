<?php

namespace App\Vuelos\Controllers;

use App\Auth\Middlewares\CeoMiddleware;
use App\Auth\Services\SessionService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Vuelos\Services\VueloService;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/ceo.middleware.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../services/vuelo.service.php';

class BorrarVueloActionController
{
    public function __construct(
        private VueloService $vueloService,
        private SessionService $sessionService
    ) {
    }

    public function borrar(array $params = [], array $query = []): void
    {
        try {
            (new CeoMiddleware($this->sessionService))->handle();
        } catch (HttpException $exception) {
            Flash::error('Necesitás permisos de CEO para borrar un vuelo.');
            RedirectResponse::to($exception->getStatusCode() === 401 ? '/auth/login' : '/', [], 303);
            return;
        }

        $vueloId = $this->vueloId($_POST);
        if ($vueloId === null) {
            Flash::error('El vuelo indicado no es válido.');
            RedirectResponse::to('/ceo/vuelos', [], 303);
            return;
        }

        try {
            $usuario = $this->sessionService->getUser() ?? [];
            $this->vueloService->borrar($vueloId, (int) ($usuario['id'] ?? 0));
            Flash::success('Vuelo borrado correctamente.');
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
        } catch (Throwable) {
            Flash::error('No pudimos borrar el vuelo. Intentalo nuevamente en unos minutos.');
        }

        RedirectResponse::to('/ceo/vuelos', [], 303);
    }

    private function vueloId(array $data): ?int
    {
        $valor = $data['vueloId'] ?? null;
        $id = is_scalar($valor)
            ? filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;

        return $id === false ? null : (int) $id;
    }
}
