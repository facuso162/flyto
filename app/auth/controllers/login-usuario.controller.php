<?php

namespace App\Auth\Controllers;

use App\Auth\Dtos\LoginUsuarioDTO;
use App\Auth\Middlewares\GuestMiddleware;
use App\Auth\Services\LoginUsuarioService;
use App\Auth\Services\SessionService;
use App\Auth\Validators\LoginUsuarioValidator;
use App\Shared\Http\JsonRequest;
use App\Shared\Http\JsonResponse;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../dtos/login-usuario.dto.php';
require_once __DIR__ . '/../middlewares/guest.middleware.php';
require_once __DIR__ . '/../services/login-usuario.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../validators/login-usuario.validator.php';
require_once __DIR__ . '/../../shared/http/json-request.php';
require_once __DIR__ . '/../../shared/http/json-response.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class LoginUsuarioController
{
    private LoginUsuarioService $loginUsuarioService;
    private SessionService $sessionService;

    public function __construct(
        LoginUsuarioService $loginUsuarioService,
        SessionService $sessionService
    ) {
        $this->loginUsuarioService = $loginUsuarioService;
        $this->sessionService = $sessionService;
    }

    public function login(array $params = [], array $query = []) {
        try {
            $middleware = new GuestMiddleware($this->sessionService);
            $middleware->handle();

            $expectsJson = JsonRequest::expectsJson();
            $data = JsonRequest::data();

            LoginUsuarioValidator::validate($data);

            $dto = new LoginUsuarioDTO(
                $data['email'],
                $data['password']
            );

            $usuario = $this->loginUsuarioService->execute($dto);

            if (!$expectsJson) {
                RedirectResponse::to('/');
                return;
            }

            JsonResponse::success([
                'message' => 'Login exitoso',
                'usuario' => $usuario
            ]);
        } catch (Throwable $exception) {
            if (!JsonRequest::expectsJson()) {
                RedirectResponse::to('/login', ['login' => 'error']);
                return;
            }

            JsonResponse::exception($exception);
        }
    }
}
