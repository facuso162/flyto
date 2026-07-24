<?php

namespace App\Aerolineas\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class CrearAerolineaValidator
{
    public static function validate(array $data): void
    {
        self::nombre($data);
        self::codigoIata($data);
        self::descripcion($data);
        self::paisId($data);
    }

    private static function nombre(array $data): void
    {
        $nombre = self::stringValue($data, 'nombre');

        if ($nombre === '') {
            self::invalid('nombre', 'El nombre de la aerolinea es obligatorio.');
        }

        if (self::length($nombre) > 100) {
            self::invalid('nombre', 'El nombre de la aerolinea no puede superar los 100 caracteres.');
        }
    }

    private static function codigoIata(array $data): void
    {
        $codigoIata = strtoupper(self::stringValue($data, 'codigoIata'));

        if ($codigoIata === '') {
            self::invalid('codigoIata', 'El codigo IATA es obligatorio.');
        }

        if (self::length($codigoIata) > 3) {
            self::invalid('codigoIata', 'El codigo IATA no puede superar los 3 caracteres.');
        }

        if (preg_match('/^[A-Z0-9]{1,3}$/', $codigoIata) !== 1) {
            self::invalid('codigoIata', 'El codigo IATA solo puede contener letras y numeros.');
        }
    }

    private static function descripcion(array $data): void
    {
        $descripcion = self::stringValue($data, 'descripcion');

        if ($descripcion === '') {
            self::invalid('descripcion', 'La descripcion es obligatoria.');
        }

        if (self::length($descripcion) > 200) {
            self::invalid('descripcion', 'La descripcion no puede superar los 200 caracteres.');
        }
    }

    private static function paisId(array $data): void
    {
        $paisId = self::stringValue($data, 'paisId');

        if ($paisId === '' || filter_var($paisId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            self::invalid('paisId', 'El pais es obligatorio.');
        }
    }

    private static function stringValue(array $data, string $field): string
    {
        $value = $data[$field] ?? '';
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private static function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }

    private static function invalid(string $field, string $message): never
    {
        throw new HttpException($message, 400, ['field' => $field]);
    }
}
