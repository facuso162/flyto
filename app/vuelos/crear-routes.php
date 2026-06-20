<?php

namespace App\Vuelos;

use App\Vuelos\Controllers\CrearVueloActionController;

require_once __DIR__ . '/controllers/crear-vuelo-action.controller.php';

return [
    'prefix' => '/ceo/vuelos',
    'routes' => [
        ['POST', '/crear', CrearVueloActionController::class, 'crear'],
    ],
];
