<?php

namespace App\Perfil\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class EditarPerfilValidator
{
    public static function validate(array $data): void
    {
        $nombre = self::value($data, 'nombre');
        $apellido = self::value($data, 'apellido');
        $telefono = self::value($data, 'telefono', true);

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

        if ($telefono !== null && (self::length($telefono) > 20 || preg_match('/^[0-9]+$/', $telefono) !== 1)) {
            throw new HttpException(
                'El telefono solo puede contener numeros y no puede superar los 20 digitos.',
                400,
                ['field' => 'telefono']
            );
        }
    }

    private static function value(array $data, string $key, bool $nullable = false): ?string
    {
        if (!array_key_exists($key, $data) || $data[$key] === null) {
            return $nullable ? null : '';
        }

        $value = trim((string) $data[$key]);
        return $nullable && $value === '' ? null : $value;
    }

    private static function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }
}
