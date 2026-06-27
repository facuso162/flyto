<?php

namespace App\Aerolineas;

use App\Aerolineas\Controllers\CrearAerolineaActionController;
use App\Aerolineas\Controllers\EditarAerolineaActionController;

require_once __DIR__ . '/controllers/crear-aerolinea-action.controller.php';
require_once __DIR__ . '/controllers/editar-aerolinea-action.controller.php';

return [
    'prefix' => '/admin/aerolineas',
    'routes' => [
        ['POST', '/crear', CrearAerolineaActionController::class, 'crear'],
        ['POST', '/editar', EditarAerolineaActionController::class, 'editar'],
    ],
];
