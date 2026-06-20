<?php

namespace App\Vuelos\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class CrearVueloValidator
{
    public static function validate(array $data): void
    {
        $codigoVuelo = self::stringValue($data, 'codigoVuelo');
        if ($codigoVuelo === '') {
            self::invalid('codigoVuelo', 'El código del vuelo es obligatorio.');
        }
        if (strlen($codigoVuelo) > 10) {
            self::invalid('codigoVuelo', 'El código del vuelo no puede superar los 10 caracteres.');
        }

        self::nonNegativeFloat($data, 'precio', 'El precio');
        self::nonNegativeInteger($data, 'asientosDisponibles', 'La cantidad de asientos disponibles');

        $fechaSalida = self::dateTime($data, 'fechaSalida', 'La fecha y hora de salida');
        $fechaLlegada = self::dateTime($data, 'fechaLlegada', 'La fecha y hora de llegada');
        $ahora = new \DateTimeImmutable();

        if ($fechaSalida <= $ahora) {
            self::invalid('fechaSalida', 'La fecha y hora de salida debe ser futura.');
        }
        if ($fechaLlegada <= $ahora) {
            self::invalid('fechaLlegada', 'La fecha y hora de llegada debe ser futura.');
        }
        if ($fechaLlegada <= $fechaSalida) {
            self::invalid('fechaLlegada', 'La llegada debe ser posterior a la salida.');
        }

        self::positiveInteger($data, 'origenCiudadId', 'La ciudad de origen');
        self::positiveInteger($data, 'destinoCiudadId', 'La ciudad de destino');
        self::nonNegativeFloat($data, 'duracionHoras', 'La duración estimada');
        self::nonNegativeInteger($data, 'distanciaKm', 'La distancia');
    }

    private static function dateTime(array $data, string $field, string $label): \DateTimeImmutable
    {
        $value = self::stringValue($data, $field);
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $value);
        $errors = \DateTimeImmutable::getLastErrors();

        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            self::invalid($field, $label . ' no tiene un formato válido.');
        }

        return $date;
    }

    private static function nonNegativeFloat(array $data, string $field, string $label): void
    {
        $value = self::stringValue($data, $field);
        if ($value === '' || !is_numeric($value) || !is_finite((float) $value) || (float) $value < 0) {
            self::invalid($field, $label . ' debe ser un número no negativo.');
        }
    }

    private static function nonNegativeInteger(array $data, string $field, string $label): void
    {
        $value = self::stringValue($data, $field);
        if ($value === '' || preg_match('/^\d+$/', $value) !== 1) {
            self::invalid($field, $label . ' debe ser un entero no negativo.');
        }
    }

    private static function positiveInteger(array $data, string $field, string $label): void
    {
        $value = self::stringValue($data, $field);
        if ($value === '' || filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            self::invalid($field, $label . ' no es válida.');
        }
    }

    private static function stringValue(array $data, string $field): string
    {
        $value = $data[$field] ?? '';
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private static function invalid(string $field, string $message): never
    {
        throw new HttpException($message, 400, ['field' => $field]);
    }
}
