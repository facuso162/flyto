<?php

namespace App\Vuelos;

use App\Vuelos\Controllers\EditarVueloActionController;

require_once __DIR__ . '/controllers/editar-vuelo-action.controller.php';

return [
    'prefix' => '/ceo/vuelos',
    'routes' => [
        ['POST', '/editar', EditarVueloActionController::class, 'editar'],
    ],
];
