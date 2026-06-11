<?php

namespace App\Auth\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class LoginUsuarioValidator
{
    public static function validate(array $data): void {
        $email = self::getStringValue($data, 'email');
        $password = self::getStringValue($data, 'password');

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new HttpException(
                'El email es obligatorio y debe tener un formato valido.',
                400,
                ['field' => 'email']
            );
        }

        if ($password === '') {
            throw new HttpException(
                'La contrasena es obligatoria.',
                400,
                ['field' => 'password']
            );
        } elseif (
            strlen($password) < 8 ||
            strlen($password) > 40 ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[^a-zA-Z0-9]/', $password)
        ) {
            throw new HttpException(
                'La contrasena debe tener entre 8 y 40 caracteres, una mayuscula, una minuscula, un numero y un caracter especial.',
                400,
                ['field' => 'password']
            );
        }
    }

    private static function getStringValue(array $data, string $key, bool $nullable = false): ?string {
        if (!array_key_exists($key, $data) || $data[$key] === null) {
            return $nullable ? null : '';
        }

        $value = trim((string) $data[$key]);

        if ($nullable && $value === '') {
            return null;
        }

        return $value;
    }
}
