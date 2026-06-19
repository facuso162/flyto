<?php

namespace App\Reservas;

use App\Reservas\Controllers\CrearReservaActionController;

require_once __DIR__ . '/controllers/crear-reserva-action.controller.php';

return [
    'prefix' => '/reservas',
    'routes' => [
        ['POST', '/crear', CrearReservaActionController::class, 'crear'],
    ],
];
