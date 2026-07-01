<?php

namespace App\Vuelos\Controllers;

use App\Ciudades\Services\CiudadService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;
use App\Vuelos\Dtos\BuscarVuelosDto;
use App\Vuelos\Services\VueloService;
use App\Vuelos\Validators\BuscarVueloValidator;
use Throwable;

require_once __DIR__ . '/../../ciudades/services/ciudad.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../dtos/buscar-vuelos.dto.php';
require_once __DIR__ . '/../services/vuelo.service.php';
require_once __DIR__ . '/../validators/buscar-vuelo.validator.php';

class BuscarVuelosPageController
{
    private CiudadService $ciudadService;
    private VueloService $vueloService;
    private ViewResponse $viewResponse;

    public function __construct(
        CiudadService $ciudadService,
        VueloService $vueloService,
        ViewResponse $viewResponse
    ) {
        $this->ciudadService = $ciudadService;
        $this->vueloService = $vueloService;
        $this->viewResponse = $viewResponse;
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        try {
            BuscarVueloValidator::validate($_GET);
        } catch (HttpException $exception) {
            $this->viewResponse->render(
                __DIR__ . '/../views/pages/buscar-vuelos.page.php',
                'Buscar vuelos - Flyto',
                [
                    'resultadoBusqueda' => null,
                    'ciudades' => $this->loadCiudades(),
                    'flash' => ['error' => $exception->getMessage()],
                ],
                200,
                $layoutPath
            );
            return;
        }

        $resultadoBusqueda = $this->vueloService->buscar(BuscarVuelosDto::fromArray($_GET));

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/buscar-vuelos.page.php',
            'Buscar vuelos - Flyto',
            [
                'resultadoBusqueda' => $resultadoBusqueda,
                'ciudades' => $this->loadCiudades(),
            ],
            200,
            $layoutPath
        );
    }

    private function loadCiudades(): array
    {
        try {
            return array_map(
                fn ($ciudad) => $ciudad->toArray(),
                $this->ciudadService->getTodas()
            );
        } catch (Throwable) {
            return [];
        }
    }
}
