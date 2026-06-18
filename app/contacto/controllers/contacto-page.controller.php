<?php

namespace App\Contacto\Controllers;

use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class ContactoPageController
{
    public function show(array $params = [], array $query = []): void
    {
        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();
        $validationErrors = $flash['validationErrors'] ?? [];

        ViewResponse::render(
            __DIR__ . '/../views/pages/contacto.page.php',
            'Contacto - Flyto',
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
