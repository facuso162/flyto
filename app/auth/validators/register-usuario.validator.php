<?php

namespace App\Auth\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/validation/password-policy.php';

class RegisterUsuarioValidator
{
    public static function validate(array $data): void {
        $email = self::getStringValue($data, 'email');
        $password = self::getStringValue($data, 'password');
        $confirmPassword = self::getStringValue($data, 'password_confirmation');
        $nombre = self::getStringValue($data, 'nombre');
        $apellido = self::getStringValue($data, 'apellido');
        $telefono = self::getStringValue($data, 'telefono', true);

        if ($email === '' || self::length($email) > 120 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
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
            !\App\Shared\Validation\PasswordPolicy::isValid($password)
        ) {
            throw new HttpException(
                'La contrasena debe tener entre 8 y 40 caracteres, una mayuscula, una minuscula, un numero y un caracter especial.',
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
        } elseif ($confirmPassword !== $password) {
            throw new HttpException(
                'La confirmacion de la contrasena no coincide.',
                400,
                ['field' => 'password_confirmation']
            );
        }

        if ($nombre === '') {
            throw new HttpException(
                'El nombre es obligatorio.',
                400,
                ['field' => 'nombre']
            );
        } elseif (self::length($nombre) > 80) {
            throw new HttpException(
                'El nombre no puede superar los 80 caracteres.',
                400,
                ['field' => 'nombre']
            );
        }

        if ($apellido === '') {
            throw new HttpException(
                'El apellido es obligatorio.',
                400,
                ['field' => 'apellido']
            );
        } elseif (self::length($apellido) > 80) {
            throw new HttpException(
                'El apellido no puede superar los 80 caracteres.',
                400,
                ['field' => 'apellido']
            );
        }

        if ($telefono !== null && (self::length($telefono) > 20 || !preg_match('/^[0-9]+$/', $telefono))) {
            throw new HttpException(
                'El telefono solo puede contener numeros.',
                400,
                ['field' => 'telefono']
            );
        }
    }

    private static function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
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
