<?php

namespace App\Perfil\Controllers;

use App\Auth\Services\SessionService;
use App\Perfil\Services\CambioContrasenaPerfilSessionService;
use App\Perfil\Services\PerfilService;
use App\Shared\Http\Flash;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;
use Throwable;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../services/cambio-contrasena-perfil-session.service.php';
require_once __DIR__ . '/../services/perfil.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class MiPerfilPageController
{
    public function __construct(
        private ViewResponse $viewResponse,
        private SessionService $sessionService,
        private PerfilService $perfilService,
        private CambioContrasenaPerfilSessionService $cambioContrasenaSession
    ) {
    }

    public function show(array $params = [], array $query = [], ?string $layoutPath = null): void
    {
        $this->sessionService->start();
        $currentUser = $this->sessionService->getUser();

        if ($currentUser === null || (int) ($currentUser['id'] ?? 0) < 1) {
            Flash::error('Necesitas iniciar sesion para editar tu perfil.');
            RedirectResponse::to('/auth/login', [], 303);
            return;
        }

        $usuarioId = (int) $currentUser['id'];
        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();
        $profileUser = $currentUser;

        try {
            $usuario = $this->perfilService->obtenerUsuario($usuarioId);
            $profileUser = [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'email' => $usuario->email,
                'telefono' => $usuario->telefono,
            ];
        } catch (Throwable) {
            $flash['error'] = 'No pudimos cargar todos los datos de tu perfil.';
        }

        $requestedModal = (string) ($query['modal'] ?? '');
        $passwordModal = null;

        if ($requestedModal === 'contrasena' || $requestedModal === 'nueva-contrasena') {
            $passwordModal = $this->cambioContrasenaSession->isAuthorized($usuarioId)
                ? 'new'
                : 'current';
        } else {
            $this->cambioContrasenaSession->revoke();
        }

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/mi-perfil.page.php',
            'Mi perfil - Flyto',
            [
                'profileUser' => $profileUser,
                'flash' => [
                    'success' => $flash['success'] ?? null,
                    'error' => $flash['error'] ?? null,
                ],
                'validationErrors' => is_array($flash['validationErrors'] ?? null)
                    ? $flash['validationErrors']
                    : [],
                'oldInput' => $oldInput,
                'passwordModal' => $passwordModal,
            ],
            200,
            $layoutPath ?? __DIR__ . '/../../shared/views/layouts/public.layout.php'
        );
    }
}
