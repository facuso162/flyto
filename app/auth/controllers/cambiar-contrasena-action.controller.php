<?php

namespace App\Auth\Controllers;

use App\Auth\Dtos\CambiarContrasenaDTO;
use App\Auth\Services\CambiarContrasenaService;
use App\Auth\Services\SessionService;
use App\Auth\Validators\CambiarContrasenaValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../dtos/cambiar-contrasena.dto.php';
require_once __DIR__ . '/../services/cambiar-contrasena.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../validators/cambiar-contrasena.validator.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class CambiarContrasenaActionController
{
    private const RECOVERY_USER_ID_KEY = 'recuperar_contrasena_usuario_id';

    public function __construct(
        private CambiarContrasenaService $cambiarContrasenaService,
        private SessionService $sessionService
    ) {
    }

    public function cambiar(array $params = [], array $query = []): void
    {
        $this->sessionService->start();

        if ($this->sessionService->isAuthenticated()) {
            RedirectResponse::to('/', [], 303);
            return;
        }

        try {
            $data = $_POST;

            CambiarContrasenaValidator::validate($data);

            $dto = new CambiarContrasenaDTO(
                (int) $data['usuario_id'],
                (string) $data['password']
            );

            $this->cambiarContrasenaService->execute($dto);
            $this->sessionService->remove(self::RECOVERY_USER_ID_KEY);

            Flash::success('Contrasena actualizada. Ya podes iniciar sesion.');
            RedirectResponse::to('/auth/login', [], 303);
        } catch (HttpException $exception) {
            if ($this->isRecoveryUserError($exception)) {
                $this->sessionService->remove(self::RECOVERY_USER_ID_KEY);
                Flash::error($exception->getMessage());
                RedirectResponse::to('/auth/recuperar-contrasena/codigo', [], 303);
                return;
            }

            Flash::error('No pudimos cambiar la contrasena. Revisa los datos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($this->safeOldInput($_POST));

            RedirectResponse::to('/auth/recuperar-contrasena/cambiar', [], 303);
        } catch (Throwable) {
            Flash::error('No pudimos cambiar la contrasena. Intentalo nuevamente en unos minutos.');
            Flash::old($this->safeOldInput($_POST));

            RedirectResponse::to('/auth/recuperar-contrasena/cambiar', [], 303);
        }
    }

    private function validationErrorsFromException(HttpException $exception): array
    {
        $details = $exception->getDetails();
        $field = $details['field'] ?? null;

        if (!is_string($field) || $field === '' || $field === 'usuario_id') {
            return ['general' => $exception->getMessage()];
        }

        return [$field => $exception->getMessage()];
    }

    private function isRecoveryUserError(HttpException $exception): bool
    {
        $field = $exception->getDetails()['field'] ?? null;

        return $field === 'usuario_id';
    }

    private function safeOldInput(array $input): array
    {
        unset($input['password'], $input['password_confirmation']);

        return $input;
    }
}
