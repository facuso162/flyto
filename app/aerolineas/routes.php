<?php

namespace App\Aerolineas;

use App\Aerolineas\Controllers\CrearAerolineaActionController;

require_once __DIR__ . '/controllers/crear-aerolinea-action.controller.php';

return [
    'prefix' => '/admin/aerolineas',
    'routes' => [
        ['POST', '/crear', CrearAerolineaActionController::class, 'crear'],
    ],
];
