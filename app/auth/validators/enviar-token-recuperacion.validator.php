<?php

namespace App\Auth\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class EnviarTokenRecuperacionValidator
{
    public static function validate(array $data): void
    {
        $email = self::getStringValue($data, 'email');

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new HttpException(
                'El email es obligatorio y debe tener un formato valido.',
                400,
                ['field' => 'email']
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
