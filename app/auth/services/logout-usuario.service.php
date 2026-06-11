<?php

namespace App\Auth\Services;

require_once __DIR__ . '/session.service.php';

class LogoutUsuarioService
{
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function execute(): void
    {
        $this->sessionService->logout();
    }
}
