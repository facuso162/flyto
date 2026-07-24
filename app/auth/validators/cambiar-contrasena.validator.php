<?php

namespace App\Auth\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/validation/password-policy.php';

class CambiarContrasenaValidator
{
    public static function validate(array $data): void
    {
        $usuarioId = self::getStringValue($data, 'usuario_id');
        $password = self::getStringValue($data, 'password');
        $confirmPassword = self::getStringValue($data, 'password_confirmation');

        if ($usuarioId === '' || filter_var($usuarioId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new HttpException(
                'El usuario de recuperacion no es valido.',
                400,
                ['field' => 'usuario_id']
            );
        }

        if ($password === '') {
            throw new HttpException(
                'La contrasena es obligatoria.',
                400,
                ['field' => 'password']
            );
        }

        if (!\App\Shared\Validation\PasswordPolicy::isValid($password)) {
            throw new HttpException(
                'La contrasena debe tener minimo 8 caracteres, una mayuscula, un numero y un caracter especial.',
                400,
                ['field' => 'password']
            );
        }

        if ($confirmPassword === '') {
            throw new HttpException(
                'La confirmacion de la contrasena es obligatoria.',
                400,
                ['field' => 'password_confirmation']
            );
        }

        if ($confirmPassword !== $password) {
            throw new HttpException(
                'La confirmacion de la contrasena no coincide.',
                400,
                ['field' => 'password_confirmation']
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
