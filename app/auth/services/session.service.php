<?php

namespace App\Auth\Services;

/**
 * TODOS:
 * - Sesion unica por usuario
 * - Expiracion automatica
 * - Refrescar sesion por actividad
 */

class SessionService
{
    private const USER_KEY = 'usuario';

    public function start(): void
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        session_unset();

        $params = session_get_cookie_params();

        if (ini_get('session.use_cookies')) {
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION[self::USER_KEY]);
    }

    public function login(array $userData): void
    {
        $this->start();

        session_unset();

        $this->regenerate();

        $this->set(self::USER_KEY, $userData);
    }

    public function logout(): void
    {
        $this->start();
        $this->destroy();
    }

    public function getUser(): ?array
    {
        return $this->get(self::USER_KEY);
    }
}
