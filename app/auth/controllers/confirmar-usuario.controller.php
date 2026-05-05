<?php

namespace App\Auth\Controllers;

use App\Auth\Services\ConfirmarUsuarioService;
use App\Auth\Middlewares\GuestMiddleware;
use App\Auth\Services\SessionService;

require_once __DIR__ . '/../services/confirmar-usuario.service.php';
require_once __DIR__ . '/../middlewares/guest.middleware.php';
require_once __DIR__ . '/../services/session.service.php';

class ConfirmarUsuarioController
{
    private ConfirmarUsuarioService $confirmarUsuarioService;

    public function __construct(
        ConfirmarUsuarioService $confirmarUsuarioService
    ) {
        $this->confirmarUsuarioService = $confirmarUsuarioService;
    }

    public function confirmar(array $params = [], array $query = []) {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $middleware = new GuestMiddleware(new SessionService());
            $middleware->handle();
            
            $token = $query['token'] ?? null;

            if (!$token) {
                throw new \Exception('Token de confirmación es requerido', 400);
            }

            $this->confirmarUsuarioService->execute($token);

            echo json_encode([
                'message' => 'Usuario confirmado exitosamente',
            ]);
        } catch (\Exception $exception) {
            // TODO: Manejar excepciones de forma simple y centralizada, consistentemente en toda la app.
            http_response_code($exception->getCode());

            echo json_encode([
                'error' => $exception->getMessage()
            ]);
        } catch (\Throwable $exception) {
            http_response_code(500);

            echo json_encode([
                'error' => 'Ocurrio un error interno del servidor.',
            ]);
        }
    }
}