<?php

namespace App\Auth\Controllers;

use App\Auth\Dtos\VerificarTokenRecuperacionDTO;
use App\Auth\Services\SessionService;
use App\Auth\Services\VerificarTokenRecuperacionService;
use App\Auth\Validators\VerificarTokenRecuperacionValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../dtos/verificar-token-recuperacion.dto.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/verificar-token-recuperacion.service.php';
require_once __DIR__ . '/../validators/verificar-token-recuperacion.validator.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class VerificarTokenRecuperacionActionController
{
    private VerificarTokenRecuperacionService $verificarTokenRecuperacionService;
    private SessionService $sessionService;

    public function __construct(
        VerificarTokenRecuperacionService $verificarTokenRecuperacionService,
        SessionService $sessionService
    ) {
        $this->verificarTokenRecuperacionService = $verificarTokenRecuperacionService;
        $this->sessionService = $sessionService;
    }

    public function verificar(array $params = [], array $query = []): void
    {
        $this->sessionService->start();

        if ($this->sessionService->isAuthenticated()) {
            RedirectResponse::to('/', [], 303);
            return;
        }

        try {
            $data = $_POST;

            VerificarTokenRecuperacionValidator::validate($data);

            $dto = new VerificarTokenRecuperacionDTO(trim((string) $data['token']));

            $this->verificarTokenRecuperacionService->execute($dto);

            Flash::success('Codigo verificado. Ingresa tu nueva contrasena.');
            RedirectResponse::to('/auth/recuperar-contrasena/cambiar', [], 303);
        } catch (HttpException $exception) {
            Flash::error('No pudimos verificar el codigo de recuperacion. Revisa los datos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));

            RedirectResponse::to('/auth/recuperar-contrasena/codigo', [], 303);
        } catch (Throwable) {
            Flash::error('No pudimos verificar el codigo de recuperacion. Intentalo nuevamente en unos minutos.');

            RedirectResponse::to('/auth/recuperar-contrasena/codigo', [], 303);
        }
    }

    private function validationErrorsFromException(HttpException $exception): array
    {
        $details = $exception->getDetails();
        $field = $details['field'] ?? null;

        if (!is_string($field) || $field === '') {
            return ['general' => $exception->getMessage()];
        }

        return [$field => $exception->getMessage()];
    }
}
