<?php

namespace App\Auth;

use App\Auth\Controllers\ConfirmarUsuarioActionController;
use App\Auth\Controllers\EnviarTokenRecuperacionActionController;
use App\Auth\Controllers\LoginUsuarioActionController;
use App\Auth\Controllers\LogoutUsuarioActionController;
use App\Auth\Controllers\RegisterUsuarioActionController;
use App\Auth\Controllers\VerificarTokenRecuperacionActionController;

require_once __DIR__ . '/../auth/controllers/confirmar-usuario-action.controller.php';
require_once __DIR__ . '/../auth/controllers/enviar-token-recuperacion-action.controller.php';
require_once __DIR__ . '/../auth/controllers/login-usuario-action.controller.php';
require_once __DIR__ . '/../auth/controllers/logout-usuario-action.controller.php';
require_once __DIR__ . '/../auth/controllers/register-usuario-action.controller.php';
require_once __DIR__ . '/../auth/controllers/verificar-token-recuperacion-action.controller.php';

return [
    'prefix' => '/auth',
    'routes' => [
        ['POST', '/registro', RegisterUsuarioActionController::class, 'register'],
        ['GET', '/confirmar', ConfirmarUsuarioActionController::class, 'confirmar'],
        ['POST', '/login', LoginUsuarioActionController::class, 'login'],
        ['POST', '/logout', LogoutUsuarioActionController::class, 'logout'],
        ['POST', '/recuperar-contrasena', EnviarTokenRecuperacionActionController::class, 'enviar'],
        ['POST', '/recuperar-contrasena/codigo', VerificarTokenRecuperacionActionController::class, 'verificar']
    ]
];
