<?php

namespace App\Auth;

use App\Auth\Controllers\ConfirmarUsuarioController;
use App\Auth\Controllers\LoginUsuarioActionController;
use App\Auth\Controllers\LogoutUsuarioActionController;
use App\Auth\Controllers\RegisterUsuarioActionController;

require_once __DIR__ . '/../auth/controllers/confirmar-usuario.controller.php';
require_once __DIR__ . '/../auth/controllers/login-usuario-action.controller.php';
require_once __DIR__ . '/../auth/controllers/logout-usuario-action.controller.php';
require_once __DIR__ . '/../auth/controllers/register-usuario-action.controller.php';

return [
    'prefix' => '',
    'routes' => [
        ['POST', '/registro', RegisterUsuarioActionController::class, 'register'],
        ['GET', '/api/auth/confirmar', ConfirmarUsuarioController::class, 'confirmar'],
        ['POST', '/login', LoginUsuarioActionController::class, 'login'],
        ['POST', '/logout', LogoutUsuarioActionController::class, 'logout']
    ]
];
