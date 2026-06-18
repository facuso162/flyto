<?php

namespace App\Auth\Controllers;

use App\Auth\Middlewares\GuestMiddleware;
use App\Auth\Services\ConfirmarUsuarioService;
use App\Auth\Services\SessionService;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../middlewares/guest.middleware.php';
require_once __DIR__ . '/../services/confirmar-usuario.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class ConfirmarUsuarioController
{
    private ConfirmarUsuarioService $confirmarUsuarioService;
    private SessionService $sessionService;

    public function __construct(
        ConfirmarUsuarioService $confirmarUsuarioService,
        SessionService $sessionService
    ) {
        $this->confirmarUsuarioService = $confirmarUsuarioService;
        $this->sessionService = $sessionService;
    }

    public function confirmar(array $params = [], array $query = []): void
    {
        try {
            $middleware = new GuestMiddleware($this->sessionService);
            $middleware->handle();

            $token = $query['token'] ?? null;

            if (!$token) {
                throw new HttpException('Token de confirmacion es requerido', 400);
            }

            $this->confirmarUsuarioService->execute($token);

            RedirectResponse::to('/cuenta-confirmada');
        } catch (Throwable) {
            RedirectResponse::to('/cuenta-confirmada', ['confirmacion' => 'error']);
        }
    }
}
