<?php

namespace App\Novedades;

use App\Novedades\Controllers\NovedadController;

require_once __DIR__ . '/controllers/novedad.controller.php';

return [
    'prefix' => '/api/novedades',
    'routes' => [
        ['GET', '/ultimas', NovedadController::class, 'getUltimas'],
        ['GET', '/vigentes', NovedadController::class, 'getVigentes'],
        ['GET', '/todas', NovedadController::class, 'getTodas'],
        ['POST', '/crear', NovedadController::class, 'crear'],
        ['POST', '/editar', NovedadController::class, 'editar'],
        ['POST', '/borrar', NovedadController::class, 'borrar'],
    ],
];
