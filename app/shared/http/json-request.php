<?php

namespace App\Shared\Http;

require_once __DIR__ . '/http-exception.php';

class JsonRequest
{
    public static function body(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (!str_contains($contentType, 'application/json')) {
            throw new HttpException('Content-Type debe ser application/json', 400);
        }

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpException('JSON invalido', 400);
        }

        if (!is_array($data)) {
            throw new HttpException('El cuerpo debe ser un objeto JSON', 400);
        }

        return $data;
    }
}
