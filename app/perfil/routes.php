<?php

namespace App\Perfil;

use App\Perfil\Controllers\PerfilActionController;

require_once __DIR__ . '/controllers/perfil-action.controller.php';

return [
    'prefix' => '/mi-perfil',
    'routes' => [
        ['POST', '/datos', PerfilActionController::class, 'actualizar'],
        ['POST', '/contrasena/verificar', PerfilActionController::class, 'verificarContrasena'],
        ['POST', '/contrasena/cambiar', PerfilActionController::class, 'cambiarContrasena'],
        ['POST', '/contrasena/cancelar', PerfilActionController::class, 'cancelarCambioContrasena'],
    ],
];
