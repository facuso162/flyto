<?php

namespace App\Vuelos\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class BuscarVueloValidator
{
    public static function validate(array $data): void
    {
        $origen = self::getRequiredStringValue($data, 'origen');
        $destino = self::getRequiredStringValue($data, 'destino');
        $fechaSalida = self::getRequiredStringValue($data, 'fechaSalida');
        $cantidadPasajeros = self::getRequiredStringValue($data, 'cantidadPasajeros');
        $precioMaximo = self::getOptionalStringValue($data, 'precioMaximo');
        $aerolineas = self::getOptionalStringValue($data, 'aerolineas');
        $orden = self::getOptionalStringValue($data, 'orden');

        if (!ctype_digit($origen)) {
            throw new HttpException(
                'El origen debe ser numerico.',
                400,
                ['field' => 'origen']
            );
        }

        if (!ctype_digit($destino)) {
            throw new HttpException(
                'El destino debe ser numerico.',
                400,
                ['field' => 'destino']
            );
        }

        self::validateFechaSalida($fechaSalida);
        self::validateCantidadPasajeros($cantidadPasajeros);

        if ($precioMaximo !== null && !ctype_digit($precioMaximo)) {
            throw new HttpException(
                'El precio maximo debe ser un numero entero.',
                400,
                ['field' => 'precioMaximo']
            );
        }

        if ($aerolineas !== null && !preg_match('/^[A-Z0-9]{2,3}(,[A-Z0-9]{2,3})*$/', $aerolineas)) {
            throw new HttpException(
                'Las aerolineas deben enviarse como codigos IATA separados por coma.',
                400,
                ['field' => 'aerolineas']
            );
        }

        if ($orden !== null && !in_array($orden, ['precio', 'duracion', 'salida'], true)) {
            throw new HttpException(
                'El orden debe ser precio, duracion o salida.',
                400,
                ['field' => 'orden']
            );
        }
    }

    private static function validateFechaSalida(string $fechaSalida): void
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $fechaSalida);
        $errors = \DateTimeImmutable::getLastErrors();

        if (!$date || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            throw new HttpException(
                'La fecha de salida debe tener formato valido.',
                400,
                ['field' => 'fechaSalida']
            );
        }

        if ($date < new \DateTimeImmutable('today')) {
            throw new HttpException(
                'La fecha de salida debe ser hoy o una fecha futura.',
                400,
                ['field' => 'fechaSalida']
            );
        }
    }

    private static function validateCantidadPasajeros(string $cantidadPasajeros): void
    {
        if (!ctype_digit($cantidadPasajeros) || (int) $cantidadPasajeros < 1) {
            throw new HttpException(
                'La cantidad de pasajeros debe ser un numero valido.',
                400,
                ['field' => 'cantidadPasajeros']
            );
        }
    }

    private static function getRequiredStringValue(array $data, string $key): string
    {
        if (!array_key_exists($key, $data) || $data[$key] === null) {
            return '';
        }

        if (is_array($data[$key])) {
            throw new HttpException(
                "El campo {$key} debe ser un string.",
                400,
                ['field' => $key]
            );
        }

        $value = trim((string) $data[$key]);

        if ($value === '') {
            throw new HttpException(
                "El campo {$key} no puede estar vacio.",
                400,
                ['field' => $key]
            );
        }

        return $value;
    }

    private static function getOptionalStringValue(array $data, string $key): ?string
    {
        if (!array_key_exists($key, $data) || $data[$key] === null) {
            return null;
        }

        if (is_array($data[$key])) {
            throw new HttpException(
                "El campo {$key} debe ser un string.",
                400,
                ['field' => $key]
            );
        }

        $value = trim((string) $data[$key]);

        if ($value === '') {
            throw new HttpException(
                "El campo {$key} no puede estar vacio si se envia.",
                400,
                ['field' => $key]
            );
        }

        return $value;
    }
}
