<?php

namespace App\Contacto;

use App\Contacto\Controllers\EnviarMensajeController;

require_once __DIR__ . '/controllers/enviar-mensaje.controller.php';

return [
    'prefix' => '/api/contacto',
    'routes' => [
        ['POST', '/enviar', EnviarMensajeController::class, 'enviar'],
    ],
];
