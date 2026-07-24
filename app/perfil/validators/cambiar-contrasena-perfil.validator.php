<?php

namespace App\Perfil\Validators;

use App\Shared\Http\HttpException;
use App\Shared\Validation\PasswordPolicy;

require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/validation/password-policy.php';

class CambiarContrasenaPerfilValidator
{
    public static function validateCurrent(array $data): void
    {
        $password = self::value($data, 'current_password');

        if ($password === '') {
            throw new HttpException(
                'La contrasena actual es obligatoria.',
                400,
                ['field' => 'current_password']
            );
        }

        if (self::length($password) > 40) {
            throw new HttpException(
                'La contrasena actual no puede superar los 40 caracteres.',
                400,
                ['field' => 'current_password']
            );
        }
    }

    public static function validateNew(array $data): void
    {
        $password = self::value($data, 'password');
        $confirmation = self::value($data, 'password_confirmation');

        if ($password === '') {
            throw new HttpException('La contrasena es obligatoria.', 400, ['field' => 'password']);
        }

        if (!PasswordPolicy::isValid($password)) {
            throw new HttpException(PasswordPolicy::message(), 400, ['field' => 'password']);
        }

        if ($confirmation === '') {
            throw new HttpException(
                'La confirmacion de la contrasena es obligatoria.',
                400,
                ['field' => 'password_confirmation']
            );
        }

        if ($confirmation !== $password) {
            throw new HttpException(
                'La confirmacion de la contrasena no coincide.',
                400,
                ['field' => 'password_confirmation']
            );
        }
    }

    private static function value(array $data, string $key): string
    {
        return trim((string) ($data[$key] ?? ''));
    }

    private static function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }
}
