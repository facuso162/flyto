<?php

namespace App\Auth\Middlewares;

use App\Auth\Services\SessionService;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class GuestMiddleware
{
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function handle(): void
    {
        $this->sessionService->start();

        if ($this->sessionService->isAuthenticated()) {
            throw new HttpException('Ya autenticado', 403);
        }
    }
}
