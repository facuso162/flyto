<?php

namespace App\Perfil\Services;

use App\Auth\Services\SessionService;

require_once __DIR__ . '/../../auth/services/session.service.php';

class CambioContrasenaPerfilSessionService
{
    private const AUTHORIZED_USER_KEY = 'perfil_cambio_contrasena_usuario_id';

    public function __construct(private SessionService $sessionService)
    {
    }

    public function authorize(int $usuarioId): void
    {
        $this->sessionService->set(self::AUTHORIZED_USER_KEY, $usuarioId);
    }

    public function isAuthorized(int $usuarioId): bool
    {
        $authorizedUserId = $this->sessionService->get(self::AUTHORIZED_USER_KEY);

        return (is_int($authorizedUserId) || ctype_digit((string) $authorizedUserId))
            && (int) $authorizedUserId === $usuarioId;
    }

    public function revoke(): void
    {
        $this->sessionService->remove(self::AUTHORIZED_USER_KEY);
    }
}
