<?php

namespace App\Auth\Controllers;

use App\Auth\Services\SessionService;
use App\Auth\Services\VerificarTokenRecuperacionService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;
use Throwable;

require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/verificar-token-recuperacion.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class RecuperarContrasenaCambiarPageController
{
    private const RECOVERY_USER_ID_KEY = 'recuperar_contrasena_usuario_id';

    public function __construct(
        private SessionService $sessionService,
        private ViewResponse $viewResponse,
        private VerificarTokenRecuperacionService $verificarTokenRecuperacionService
    ) {
    }

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->sessionService->start();
        
        // TODO - Chequear si de esto se hace cargo la funcion show o si el router ya lo hace
        if ($this->sessionService->isAuthenticated()) {
            RedirectResponse::to('/');
            return;
        }

        $usuarioId = $this->sessionService->get(self::RECOVERY_USER_ID_KEY);

        try {
            if (!is_int($usuarioId) && !ctype_digit((string) $usuarioId)) {
                throw new HttpException('Primero verifica el codigo de recuperacion.', 400);
            }

            $usuario = $this->verificarTokenRecuperacionService->validarUsuarioConToken((int) $usuarioId);
        } catch (HttpException $exception) {
            $this->sessionService->remove(self::RECOVERY_USER_ID_KEY);
            Flash::error($exception->getMessage());
            RedirectResponse::to('/auth/recuperar-contrasena/codigo', [], 303);
            return;
        } catch (Throwable) {
            $this->sessionService->remove(self::RECOVERY_USER_ID_KEY);
            Flash::error('No pudimos cargar el formulario de cambio de contrasena.');
            RedirectResponse::to('/auth/recuperar-contrasena/codigo', [], 303);
            return;
        }

        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();
        $validationErrors = $flash['validationErrors'] ?? [];

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/recuperar-contrasena-cambiar.page.php',
            'Cambiar contrasena - Flyto',
            [
                'usuarioId' => (int) $usuario->id,
                'flash' => [
                    'success' => $flash['success'] ?? null,
                    'error' => $flash['error'] ?? null,
                ],
                'oldInput' => is_array($oldInput) ? $oldInput : [],
                'validationErrors' => is_array($validationErrors) ? $validationErrors : [],
            ],
            200,
            $layoutPath
        );
    }
}
