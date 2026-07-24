<?php

namespace App\Shared\Validation;

final class PasswordPolicy
{
    public static function isValid(string $password): bool
    {
        $length = function_exists('mb_strlen') ? mb_strlen($password) : strlen($password);

        return $length >= 8
            && $length <= 40
            && preg_match('/[A-Z]/', $password) === 1
            && preg_match('/[a-z]/', $password) === 1
            && preg_match('/[0-9]/', $password) === 1
            && preg_match('/[^a-zA-Z0-9]/', $password) === 1;
    }

    public static function message(): string
    {
        return 'La contrasena debe tener entre 8 y 40 caracteres, una mayuscula, una minuscula, un numero y un caracter especial.';
    }
}
