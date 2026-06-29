<?php

namespace App\Auth\Controllers;

use App\Auth\Dtos\EnviarTokenRecuperacionDTO;
use App\Auth\Services\EnviarTokenRecuperacionService;
use App\Auth\Services\SessionService;
use App\Auth\Validators\EnviarTokenRecuperacionValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../dtos/enviar-token-recuperacion.dto.php';
require_once __DIR__ . '/../services/enviar-token-recuperacion.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../validators/enviar-token-recuperacion.validator.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class EnviarTokenRecuperacionActionController
{
    private const RECOVERY_USER_ID_KEY = 'recuperar_contrasena_usuario_id';

    private EnviarTokenRecuperacionService $enviarTokenRecuperacionService;
    private SessionService $sessionService;

    public function __construct(
        EnviarTokenRecuperacionService $enviarTokenRecuperacionService,
        SessionService $sessionService
    ) {
        $this->enviarTokenRecuperacionService = $enviarTokenRecuperacionService;
        $this->sessionService = $sessionService;
    }

    public function enviar(array $params = [], array $query = []): void
    {
        $this->sessionService->start();

        if ($this->sessionService->isAuthenticated()) {
            RedirectResponse::to('/', [], 303);
            return;
        }

        try {
            $this->sessionService->remove(self::RECOVERY_USER_ID_KEY);

            $data = $_POST;

            EnviarTokenRecuperacionValidator::validate($data);

            $dto = new EnviarTokenRecuperacionDTO(trim((string) $data['email']));

            $this->enviarTokenRecuperacionService->execute($dto);

            Flash::success('Te enviamos un codigo de recuperacion a tu email.');
            RedirectResponse::to('/auth/recuperar-contrasena/codigo', [], 303);
        } catch (HttpException $exception) {
            Flash::error('No pudimos enviar el email de recuperacion. Revisa los datos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($this->safeOldInput($_POST));

            RedirectResponse::to('/auth/recuperar-contrasena', [], 303);
        } catch (Throwable) {
            Flash::error('No pudimos enviar el email de recuperacion. Intentalo nuevamente en unos minutos.');
            Flash::old($this->safeOldInput($_POST));

            RedirectResponse::to('/auth/recuperar-contrasena', [], 303);
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

    private function safeOldInput(array $input): array
    {
        unset($input['password'], $input['password_confirmation']);

        return $input;
    }
}
