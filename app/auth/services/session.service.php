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
    private const INTENDED_PATH_KEY = 'auth_intended_path';

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

        $intendedPath = $this->get(self::INTENDED_PATH_KEY);
        session_unset();

        $this->regenerate();

        $this->set(self::USER_KEY, $userData);

        if (is_string($intendedPath) && $this->isSafeInternalPath($intendedPath)) {
            $this->set(self::INTENDED_PATH_KEY, $intendedPath);
        }
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

    public function updateUser(array $userData): void
    {
        $currentUser = $this->getUser();

        if ($currentUser === null) {
            return;
        }

        $this->set(self::USER_KEY, array_replace($currentUser, $userData));
    }

    public function rememberIntendedPath(string $path): void
    {
        $this->start();

        if ($this->isSafeInternalPath($path)) {
            $this->set(self::INTENDED_PATH_KEY, $path);
        }
    }

    public function consumeIntendedPath(): ?string
    {
        $this->start();

        $path = $this->get(self::INTENDED_PATH_KEY);
        $this->remove(self::INTENDED_PATH_KEY);

        return is_string($path) && $this->isSafeInternalPath($path)
            ? $path
            : null;
    }

    private function isSafeInternalPath(string $path): bool
    {
        if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//')) {
            return false;
        }

        if (str_contains($path, "\r") || str_contains($path, "\n")) {
            return false;
        }

        $parts = parse_url($path);

        return is_array($parts)
            && !isset($parts['scheme'])
            && !isset($parts['host'])
            && !isset($parts['user'])
            && !isset($parts['pass']);
    }
}
