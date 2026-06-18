<?php

namespace App\Shared\Http;

class ViewResponse
{
    public static function render(string $viewPath, string $title, array $viewData = [], int $statusCode = 200): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        http_response_code($statusCode);

        $basePath = self::basePath();
        $currentPath = self::currentPath($basePath);
        $pageTitle = $title;
        $currentUser = $_SESSION['usuario'] ?? null;
        $isAuthenticated = $currentUser !== null;

        ob_start();
        extract($viewData, EXTR_SKIP);
        require $viewPath;
        $content = ob_get_clean();

        require __DIR__ . '/../views/layouts/public.layout.php';
    }

    private static function basePath(): string
    {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

        return $scriptDir === '/' ? '' : rtrim($scriptDir, '/');
    }

    private static function currentPath(string $basePath): string
    {
        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
            $requestPath = substr($requestPath, strlen($basePath)) ?: '/';
        }

        return $requestPath !== '/' ? rtrim($requestPath, '/') : '/';
    }
}
