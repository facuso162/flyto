<?php

namespace App\Shared\Http;

use Throwable;

require_once __DIR__ . '/http-exception.php';

class JsonResponse
{
    public static function success(array $payload = [], int $statusCode = 200): void
    {
        self::send($payload, $statusCode);
    }

    public static function error(string $message, int $statusCode = 500, array $details = []): void
    {
        $payload = [
            'error' => $message
        ];

        if ($details !== []) {
            $payload['details'] = $details;
        }

        self::send($payload, $statusCode);
    }

    public static function exception(Throwable $exception): void
    {
        if ($exception instanceof HttpException) {
            self::error(
                $exception->getMessage(),
                $exception->getStatusCode(),
                $exception->getDetails()
            );

            return;
        }

        self::error('Ocurrio un error interno del servidor.', 500);
    }

    private static function send(array $payload, int $statusCode): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);

        echo json_encode($payload);
    }
}
