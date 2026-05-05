<?php

namespace App\Auth\Controllers;

use App\Auth\Validators\RegisterUsuarioValidator;
use App\Auth\Dtos\RegisterUsuarioDTO;
use App\Auth\Services\RegisterUsuarioService;
use App\Auth\Middlewares\GuestMiddleware;
use App\Auth\Services\SessionService;

require_once __DIR__ . '/../validators/register-usuario.validator.php';
require_once __DIR__ . '/../dtos/register-usuario.dto.php';
require_once __DIR__ . '/../services/register-usuario.service.php';
require_once __DIR__ . '/../middlewares/guest.middleware.php';
require_once __DIR__ . '/../services/session.service.php';

class RegisterUsuarioController
{
    private RegisterUsuarioService $registerUsuarioService;

    public function __construct(
        RegisterUsuarioService $registerUsuarioService
    ) {
        $this->registerUsuarioService = $registerUsuarioService;
    }

    public function register(array $params = [], array $query = []) {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $middleware = new GuestMiddleware(new SessionService());
            $middleware->handle();

            $data = $this->getJsonBody();

            RegisterUsuarioValidator::validate($data);

            $dto = new RegisterUsuarioDTO(
                $data['email'],
                $data['password'],
                $data['nombre'],
                $data['apellido'],
                $data['telefono']
            );

            $this->registerUsuarioService->execute($dto);

            echo json_encode([
                'message' => 'Revisa tu email para confirmar la cuenta',
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

    private function getJsonBody(): array {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (!str_contains($contentType, 'application/json')) {
            throw new \Exception('Content-Type debe ser application/json', 400);
        }

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON invalido', 400);
        }

        if (!is_array($data)) {
            throw new \Exception('El cuerpo debe ser un objeto JSON', 400);
        }

        return $data;
    }
}
