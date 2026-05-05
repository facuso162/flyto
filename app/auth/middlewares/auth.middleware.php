<?php

namespace App\Auth\Middlewares;

use App\Auth\Services\SessionService;

require_once __DIR__ . '/../services/session.service.php';

class AuthMiddleware
{
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function handle(): void
    {
        $this->sessionService->start();

        if (!$this->sessionService->isAuthenticated()) {
            http_response_code(401);

            echo json_encode([
                'error' => 'No autenticado'
            ]);

            exit;
        }
    }
}