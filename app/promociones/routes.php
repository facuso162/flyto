<?php

namespace App\Promociones;

use App\Promociones\Controllers\CrearPromocionActionController;

require_once __DIR__ . '/controllers/crear-promocion-action.controller.php';

return [
    'prefix' => '/ceo/promociones',
    'routes' => [
        ['POST', '/crear', CrearPromocionActionController::class, 'crear'],
    ],
];
