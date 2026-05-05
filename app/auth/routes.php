<?php

namespace App\Auth;

use App\Auth\Controllers\RegisterUsuarioController;
use App\Auth\Controllers\ConfirmarUsuarioController;
use App\Auth\Controllers\LoginUsuarioController;

require_once __DIR__ . '/../auth/controllers/register-usuario.controller.php';
require_once __DIR__ . '/../auth/controllers/confirmar-usuario.controller.php';
require_once __DIR__ . '/../auth/controllers/login-usuario.controller.php';

return [
    'prefix' => '/api/auth',
    'routes' => [
        ['POST', '/register', RegisterUsuarioController::class, 'register'],
        ['GET', '/confirmar', ConfirmarUsuarioController::class, 'confirmar'],
        ['POST', '/login', LoginUsuarioController::class, 'login']
    ]
];
