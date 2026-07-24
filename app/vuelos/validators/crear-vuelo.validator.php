<?php

namespace App\Vuelos\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class CrearVueloValidator
{
    public static function validate(array $data): void
    {
        $codigo = self::stringValue($data, 'codigoVuelo');
        if ($codigo === '' || self::length($codigo) > 10) self::invalid('codigoVuelo', 'El codigo del vuelo es obligatorio y no puede superar 10 caracteres.');

        self::decimal($data, 'precio', 99999999.99, 'El precio');
        self::integer($data, 'asientosDisponibles', 0, 2147483647, 'La cantidad de asientos disponibles');
        $salida = self::dateTime($data, 'fechaSalida');
        $llegada = self::dateTime($data, 'fechaLlegada');
        $ahora = new \DateTimeImmutable();
        if ($salida <= $ahora) self::invalid('fechaSalida', 'La fecha y hora de salida debe ser futura.');
        if ($llegada <= $ahora || $llegada <= $salida) self::invalid('fechaLlegada', 'La llegada debe ser posterior a la salida y futura.');

        $origen = self::positiveInteger($data, 'origenCiudadId');
        $destino = self::positiveInteger($data, 'destinoCiudadId');
        if ($origen === $destino) self::invalid('destinoCiudadId', 'La ciudad de origen y destino deben ser distintas.');
        if (self::durationHours($salida, $llegada) > 999.99) self::invalid('duracionHoras', 'La duracion estimada no puede superar 999.99 horas.');
        self::integer($data, 'distanciaKm', 0, 2147483647, 'La distancia');
    }

    private static function dateTime(array $data, string $field): \DateTimeImmutable
    {
        $value = self::stringValue($data, $field);
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i', $value);
        $errors = \DateTimeImmutable::getLastErrors();
        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) || $date->format('Y-m-d\\TH:i') !== $value) self::invalid($field, 'La fecha no tiene un formato valido.');
        return $date;
    }

    private static function decimal(array $data, string $field, float $max, string $label): void
    {
        $value = self::stringValue($data, $field);
        if ($value === '' || preg_match('/^\d+(?:\.\d{1,2})?$/', $value) !== 1 || (float) $value > $max) self::invalid($field, $label . ' debe respetar el rango y tener hasta dos decimales.');
    }

    private static function durationHours(\DateTimeImmutable $salida, \DateTimeImmutable $llegada): float
    {
        return ($llegada->getTimestamp() - $salida->getTimestamp()) / 3600;
    }

    private static function integer(array $data, string $field, int $min, int $max, string $label): void
    {
        $value = self::stringValue($data, $field);
        if ($value === '' || preg_match('/^\d+$/', $value) !== 1 || filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]) === false) self::invalid($field, $label . ' debe ser un entero dentro del rango permitido.');
    }

    private static function positiveInteger(array $data, string $field): int
    {
        $value = self::stringValue($data, $field);
        if ($value === '' || filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) self::invalid($field, 'El valor debe ser un entero positivo.');
        return (int) $value;
    }

    private static function stringValue(array $data, string $field): string
    {
        $value = $data[$field] ?? '';
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private static function length(string $value): int { return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value); }
    private static function invalid(string $field, string $message): never { throw new HttpException($message, 400, ['field' => $field]); }
}
