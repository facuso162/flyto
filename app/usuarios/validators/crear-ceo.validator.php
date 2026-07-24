<?php

namespace App\Usuarios\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/validation/password-policy.php';

class CrearCeoValidator
{
    public static function validate(array $data): void
    {
        $nombre = self::getStringValue($data, 'nombre');
        $apellido = self::getStringValue($data, 'apellido');
        $email = self::getStringValue($data, 'email');
        $password = self::getStringValue($data, 'password');
        $confirmPassword = self::getStringValue($data, 'password_confirmation');
        $aerolineaId = self::getStringValue($data, 'aerolineaId');

        if ($nombre === '') {
            throw new HttpException('El nombre es obligatorio.', 400, ['field' => 'nombre']);
        }

        if (self::length($nombre) > 80) {
            throw new HttpException('El nombre no puede superar los 80 caracteres.', 400, ['field' => 'nombre']);
        }

        if ($apellido === '') {
            throw new HttpException('El apellido es obligatorio.', 400, ['field' => 'apellido']);
        }

        if (self::length($apellido) > 80) {
            throw new HttpException('El apellido no puede superar los 80 caracteres.', 400, ['field' => 'apellido']);
        }

        if ($email === '') {
            throw new HttpException('El correo electronico es obligatorio.', 400, ['field' => 'email']);
        }

        if (self::length($email) > 120 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new HttpException('El correo electronico debe tener un formato valido.', 400, ['field' => 'email']);
        }

        if ($password === '') {
            throw new HttpException('La contrasena es obligatoria.', 400, ['field' => 'password']);
        }

        if (
            !\App\Shared\Validation\PasswordPolicy::isValid($password)
        ) {
            throw new HttpException(
                \App\Shared\Validation\PasswordPolicy::message(),
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

        if ($aerolineaId === '' || filter_var($aerolineaId, FILTER_VALIDATE_INT) === false || (int) $aerolineaId <= 0) {
            throw new HttpException(
                'La aerolinea seleccionada debe ser valida.',
                400,
                ['field' => 'aerolineaId']
            );
        }
    }

    private static function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }

    private static function getStringValue(array $data, string $key): string
    {
        if (!array_key_exists($key, $data) || $data[$key] === null) {
            return '';
        }

        return trim((string) $data[$key]);
    }
}
