<?php

namespace App\Vuelos\Controllers;

use App\Auth\Services\SessionService;
use App\Ciudades\Services\CiudadService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;
use App\Vuelos\Services\VueloService;
use Throwable;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../ciudades/services/ciudad.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/vuelo.service.php';

class EditarVueloPageController
{
    public function __construct(
        private CiudadService $ciudadService,
        private VueloService $vueloService,
        private SessionService $sessionService,
        private ViewResponse $viewResponse
    ) {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $usuario = $this->sessionService->getUser() ?? [];

        try {
            $vueloId = $this->vueloId($query);
            $vuelo = $this->vueloService->getEditableByCeoId($vueloId, (int) ($usuario['id'] ?? 0));
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
            RedirectResponse::to('/ceo/vuelos', [], 303);
            return;
        } catch (Throwable) {
            Flash::error('No pudimos cargar el vuelo para editar.');
            RedirectResponse::to('/ceo/vuelos', [], 303);
            return;
        }

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/editar-vuelo.page.php',
            'Editar vuelo - Panel CEO - Flyto',
            [
                'vuelo' => $vuelo,
                'ciudades' => $this->ciudadService->getTodas(),
                'flash' => Flash::consume(),
                'oldInput' => Flash::consumeOld(),
            ],
            200,
            $layoutPath
        );
    }

    private function vueloId(array $query): int
    {
        $value = $query['id'] ?? null;
        $id = is_scalar($value)
            ? filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;

        if ($id === false) {
            throw new HttpException('El vuelo solicitado no es válido.', 400);
        }

        return (int) $id;
    }
}
