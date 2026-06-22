<?php

namespace App\Promociones;

use App\Promociones\Controllers\BorrarPromocionActionController;
use App\Promociones\Controllers\CrearPromocionActionController;
use App\Promociones\Controllers\EditarPromocionActionController;

require_once __DIR__ . '/controllers/borrar-promocion-action.controller.php';
require_once __DIR__ . '/controllers/crear-promocion-action.controller.php';
require_once __DIR__ . '/controllers/editar-promocion-action.controller.php';

return [
    'prefix' => '/ceo/promociones',
    'routes' => [
        ['POST', '/borrar', BorrarPromocionActionController::class, 'borrar'],
        ['POST', '/crear', CrearPromocionActionController::class, 'crear'],
        ['POST', '/editar', EditarPromocionActionController::class, 'editar'],
    ],
];
