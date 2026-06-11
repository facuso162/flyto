<?php

namespace App\Auth\Controllers;

use App\Auth\Dtos\RegisterUsuarioDTO;
use App\Auth\Middlewares\GuestMiddleware;
use App\Auth\Services\RegisterUsuarioService;
use App\Auth\Services\SessionService;
use App\Auth\Validators\RegisterUsuarioValidator;
use App\Shared\Http\JsonRequest;
use App\Shared\Http\JsonResponse;
use Throwable;

require_once __DIR__ . '/../dtos/register-usuario.dto.php';
require_once __DIR__ . '/../middlewares/guest.middleware.php';
require_once __DIR__ . '/../services/register-usuario.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../validators/register-usuario.validator.php';
require_once __DIR__ . '/../../shared/http/json-request.php';
require_once __DIR__ . '/../../shared/http/json-response.php';

class RegisterUsuarioController
{
    private RegisterUsuarioService $registerUsuarioService;
    private SessionService $sessionService;

    public function __construct(
        RegisterUsuarioService $registerUsuarioService,
        SessionService $sessionService
    ) {
        $this->registerUsuarioService = $registerUsuarioService;
        $this->sessionService = $sessionService;
    }

    public function register(array $params = [], array $query = []) {
        try {
            $middleware = new GuestMiddleware($this->sessionService);
            $middleware->handle();

            $data = JsonRequest::body();

            RegisterUsuarioValidator::validate($data);

            $dto = new RegisterUsuarioDTO(
                $data['email'],
                $data['password'],
                $data['nombre'],
                $data['apellido'],
                $data['telefono'] ?? null
            );

            $this->registerUsuarioService->execute($dto);

            JsonResponse::success([
                'message' => 'Revisa tu email para confirmar la cuenta',
            ], 201);
        } catch (Throwable $exception) {
            JsonResponse::exception($exception);
        }
    }
}
