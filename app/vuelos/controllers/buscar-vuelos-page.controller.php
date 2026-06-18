<?php

namespace App\Vuelos\Controllers;

use App\Shared\Http\ViewResponse;
use App\Vuelos\Dtos\BuscarVuelosDto;
use App\Vuelos\Services\VueloService;
use App\Vuelos\Validators\BuscarVueloValidator;

require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../dtos/buscar-vuelos.dto.php';
require_once __DIR__ . '/../services/vuelo.service.php';
require_once __DIR__ . '/../validators/buscar-vuelo.validator.php';

class BuscarVuelosPageController
{
    private VueloService $vueloService;
    private ViewResponse $viewResponse;

    public function __construct(VueloService $vueloService, ViewResponse $viewResponse)
    {
        $this->vueloService = $vueloService;
        $this->viewResponse = $viewResponse;
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        BuscarVueloValidator::validate($_GET);

        $resultadoBusqueda = $this->vueloService->buscar(BuscarVuelosDto::fromArray($_GET));

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/buscar-vuelos.page.php',
            'Buscar vuelos - Flyto',
            ['resultadoBusqueda' => $resultadoBusqueda],
            200,
            $layoutPath
        );
    }
}
