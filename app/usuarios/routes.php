<?php

namespace App\Usuarios;

use App\Usuarios\Controllers\CrearCeoActionController;

require_once __DIR__ . '/controllers/crear-ceo-action.controller.php';

return [
    'prefix' => '/admin/ceos',
    'routes' => [
        ['POST', '/crear', CrearCeoActionController::class, 'crear'],
    ],
];
