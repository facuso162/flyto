<?php

namespace App\Perfil\Controllers;

use App\Auth\Services\SessionService;
use App\Perfil\Dtos\EditarPerfilDTO;
use App\Perfil\Services\CambioContrasenaPerfilSessionService;
use App\Perfil\Services\PerfilService;
use App\Perfil\Validators\CambiarContrasenaPerfilValidator;
use App\Perfil\Validators\EditarPerfilValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../dtos/editar-perfil.dto.php';
require_once __DIR__ . '/../services/cambio-contrasena-perfil-session.service.php';
require_once __DIR__ . '/../services/perfil.service.php';
require_once __DIR__ . '/../validators/cambiar-contrasena-perfil.validator.php';
require_once __DIR__ . '/../validators/editar-perfil.validator.php';

class PerfilActionController
{
    public function __construct(
        private PerfilService $perfilService,
        private SessionService $sessionService,
        private CambioContrasenaPerfilSessionService $cambioContrasenaSession
    ) {
    }

    public function actualizar(array $params = [], array $query = []): void
    {
        $usuarioId = $this->authenticatedUserId();
        if ($usuarioId === null) {
            return;
        }

        try {
            EditarPerfilValidator::validate($_POST);

            $nombre = trim((string) $_POST['nombre']);
            $apellido = trim((string) $_POST['apellido']);
            $telefono = trim((string) ($_POST['telefono'] ?? ''));
            $telefono = $telefono === '' ? null : $telefono;

            $this->perfilService->editarDatos(new EditarPerfilDTO(
                $usuarioId,
                $nombre,
                $apellido,
                $telefono
            ));
            $this->sessionService->updateUser([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'telefono' => $telefono,
            ]);

            Flash::success('Tus datos personales se actualizaron correctamente.');
            RedirectResponse::to('/mi-perfil/datos', [], 303);
        } catch (HttpException $exception) {
            Flash::error('No pudimos actualizar tus datos. Revisa los campos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($this->profileOldInput($_POST));
            RedirectResponse::to('/mi-perfil/datos', [], 303);
        } catch (Throwable) {
            Flash::error('No pudimos actualizar tus datos. Intentalo nuevamente en unos minutos.');
            Flash::old($this->profileOldInput($_POST));
            RedirectResponse::to('/mi-perfil/datos', [], 303);
        }
    }

    public function verificarContrasena(array $params = [], array $query = []): void
    {
        $usuarioId = $this->authenticatedUserId();
        if ($usuarioId === null) {
            return;
        }

        $this->cambioContrasenaSession->revoke();

        try {
            CambiarContrasenaPerfilValidator::validateCurrent($_POST);
            $this->perfilService->verificarContrasenaActual(
                $usuarioId,
                (string) $_POST['current_password']
            );
            $this->cambioContrasenaSession->authorize($usuarioId);
            RedirectResponse::to('/mi-perfil/datos', ['modal' => 'nueva-contrasena'], 303);
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
            Flash::validationErrors($this->validationErrorsFromException($exception));
            RedirectResponse::to('/mi-perfil/datos', ['modal' => 'contrasena'], 303);
        } catch (Throwable) {
            Flash::error('No pudimos validar tu contrasena. Intentalo nuevamente en unos minutos.');
            RedirectResponse::to('/mi-perfil/datos', ['modal' => 'contrasena'], 303);
        }
    }

    public function cambiarContrasena(array $params = [], array $query = []): void
    {
        $usuarioId = $this->authenticatedUserId();
        if ($usuarioId === null) {
            return;
        }

        if (!$this->cambioContrasenaSession->isAuthorized($usuarioId)) {
            Flash::error('Primero debes validar tu contrasena actual.');
            RedirectResponse::to('/mi-perfil/datos', ['modal' => 'contrasena'], 303);
            return;
        }

        try {
            CambiarContrasenaPerfilValidator::validateNew($_POST);
            $this->perfilService->cambiarContrasena($usuarioId, (string) $_POST['password']);
            $this->cambioContrasenaSession->revoke();

            Flash::success('Tu contrasena se actualizo correctamente.');
            RedirectResponse::to('/mi-perfil/datos', [], 303);
        } catch (HttpException $exception) {
            Flash::error('No pudimos cambiar tu contrasena. Revisa los campos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            RedirectResponse::to('/mi-perfil/datos', ['modal' => 'nueva-contrasena'], 303);
        } catch (Throwable) {
            Flash::error('No pudimos cambiar tu contrasena. Intentalo nuevamente en unos minutos.');
            RedirectResponse::to('/mi-perfil/datos', ['modal' => 'nueva-contrasena'], 303);
        }
    }

    public function cancelarCambioContrasena(array $params = [], array $query = []): void
    {
        $this->sessionService->start();
        $this->cambioContrasenaSession->revoke();
        RedirectResponse::to('/mi-perfil/datos', [], 303);
    }

    private function authenticatedUserId(): ?int
    {
        $this->sessionService->start();
        $user = $this->sessionService->getUser();
        $usuarioId = (int) ($user['id'] ?? 0);

        if ($user === null || $usuarioId < 1) {
            $this->cambioContrasenaSession->revoke();
            Flash::error('Necesitas iniciar sesion para editar tu perfil.');
            RedirectResponse::to('/auth/login', [], 303);
            return null;
        }

        return $usuarioId;
    }

    private function validationErrorsFromException(HttpException $exception): array
    {
        $field = $exception->getDetails()['field'] ?? null;

        return is_string($field) && $field !== ''
            ? [$field => $exception->getMessage()]
            : ['general' => $exception->getMessage()];
    }

    private function profileOldInput(array $data): array
    {
        $oldInput = [];

        foreach (['nombre', 'apellido', 'telefono'] as $field) {
            if (isset($data[$field]) && is_scalar($data[$field])) {
                $oldInput[$field] = trim((string) $data[$field]);
            }
        }

        return $oldInput;
    }
}
