<?php

namespace App\Promociones;

use App\Promociones\Controllers\BorrarPromocionActionController;
use App\Promociones\Controllers\CrearPromocionActionController;
use App\Promociones\Controllers\DesactivarPromocionActionController;
use App\Promociones\Controllers\EditarPromocionActionController;
use App\Promociones\Controllers\SolicitarActivacionActionController;

require_once __DIR__ . '/controllers/borrar-promocion-action.controller.php';
require_once __DIR__ . '/controllers/crear-promocion-action.controller.php';
require_once __DIR__ . '/controllers/desactivar-promocion-action.controller.php';
require_once __DIR__ . '/controllers/editar-promocion-action.controller.php';
require_once __DIR__ . '/controllers/solicitar-activacion-action.controller.php';

return [
    'prefix' => '/ceo/promociones',
    'routes' => [
        ['POST', '/borrar', BorrarPromocionActionController::class, 'borrar'],
        ['POST', '/crear', CrearPromocionActionController::class, 'crear'],
        ['POST', '/desactivar', DesactivarPromocionActionController::class, 'desactivar'],
        ['POST', '/editar', EditarPromocionActionController::class, 'editar'],
        ['POST', '/solicitar-activacion', SolicitarActivacionActionController::class, 'solicitar'],
    ],
];
