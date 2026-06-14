<?php

namespace App\Shared\Http;

require_once __DIR__ . '/http-exception.php';

class JsonRequest
{
    public static function data(): array
    {
        if (!self::isJsonRequest()) {
            return $_POST;
        }

        return self::body();
    }

    public static function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

        return self::isJsonRequest()
            || str_contains($accept, 'application/json')
            || $accept === ''
            || $accept === '*/*';
    }

    public static function body(): array
    {
        if (!self::isJsonRequest()) {
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

    private static function isJsonRequest(): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        return str_contains($contentType, 'application/json');
    }
}
