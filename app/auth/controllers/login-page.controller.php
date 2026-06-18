<?php

namespace App\Auth\Controllers;

use App\Shared\Http\Flash;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class LoginPageController
{
    public function show(array $params = [], array $query = []): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario'])) {
            RedirectResponse::to('/');
            return;
        }

        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();
        $validationErrors = $flash['validationErrors'] ?? [];

        ViewResponse::render(
            __DIR__ . '/../views/pages/login.page.php',
            'Ingresar - Flyto',
            [
                'flash' => [
                    'success' => $flash['success'] ?? null,
                    'error' => $flash['error'] ?? null,
                ],
                'oldInput' => is_array($oldInput) ? $oldInput : [],
                'validationErrors' => is_array($validationErrors) ? $validationErrors : [],
            ]
        );
    }
}
