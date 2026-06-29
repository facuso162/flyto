<?php

namespace App\Auth\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class VerificarTokenRecuperacionValidator
{
    public static function validate(array $data): void
    {
        $token = self::getStringValue($data, 'token');

        if ($token === '') {
            throw new HttpException(
                'El codigo de recuperacion es obligatorio.',
                400,
                ['field' => 'token']
            );
        }
    }

    private static function getStringValue(array $data, string $key): string
    {
        if (!array_key_exists($key, $data) || $data[$key] === null) {
            return '';
        }

        return trim((string) $data[$key]);
    }
}
