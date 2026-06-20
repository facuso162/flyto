<?php

namespace App\Vuelos;

use App\Vuelos\Controllers\BorrarVueloActionController;

require_once __DIR__ . '/controllers/borrar-vuelo-action.controller.php';

return [
    'prefix' => '/ceo/vuelos',
    'routes' => [
        ['POST', '/borrar', BorrarVueloActionController::class, 'borrar'],
    ],
];
