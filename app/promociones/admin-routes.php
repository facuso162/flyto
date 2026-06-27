<?php

namespace App\Promociones;

use App\Promociones\Controllers\AprobarPromocionActionController;
use App\Promociones\Controllers\DenegarPromocionActionController;

require_once __DIR__ . '/controllers/aprobar-promocion-action.controller.php';
require_once __DIR__ . '/controllers/denegar-promocion-action.controller.php';

return [
    'prefix' => '',
    'routes' => [
        ['POST', '/admin/promociones/aprobar', AprobarPromocionActionController::class, 'aprobar'],
        ['POST', '/admin/promociones/denegar', DenegarPromocionActionController::class, 'denegar'],
    ],
];
