<?php

namespace App\Promociones\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class PromocionValidator
{
    public static function crear(array $data): void
    {
        self::descripcion($data);
        self::descuento($data);
    }

    public static function editar(array $data): void
    {
        self::id($data);
        self::descripcion($data);
        self::descuento($data);
    }

    public static function edicionId(array $data): void
    {
        self::id($data);
    }

    public static function activacionId(array $data): void
    {
        self::id($data);
    }

    public static function activar(array $data): void
    {
        self::id($data);
        self::fechaFin($data);
    }

    private static function id(array $data): void
    {
        $id = self::stringValue($data, 'id');

        if ($id === '' || filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new HttpException('El id de la promocion es obligatorio y debe ser numerico.', 400, ['field' => 'id']);
        }
    }

    private static function descripcion(array $data): void
    {
        $descripcion = self::stringValue($data, 'descripcion');

        if ($descripcion === '') {
            throw new HttpException('La descripcion es obligatoria.', 400, ['field' => 'descripcion']);
        }

        $longitud = function_exists('mb_strlen') ? mb_strlen($descripcion) : strlen($descripcion);

        if ($longitud > 200) {
            throw new HttpException('La descripcion no puede superar los 200 caracteres.', 400, ['field' => 'descripcion']);
        }
    }

    private static function descuento(array $data): void
    {
        $descuento = self::stringValue($data, 'descuento');

        if ($descuento === '' || filter_var($descuento, FILTER_VALIDATE_INT) === false) {
            throw new HttpException('El descuento debe ser un numero entero.', 400, ['field' => 'descuento']);
        }

        $valor = (int) $descuento;
        if ($valor < 0 || $valor > 100) {
            throw new HttpException('El descuento debe estar entre 0 y 100.', 400, ['field' => 'descuento']);
        }
    }

    private static function fechaFin(array $data): void
    {
        $valor = self::stringValue($data, 'fecha_fin');

        if ($valor === '') {
            throw new HttpException('La fecha de fin es obligatoria.', 400, ['field' => 'fecha_fin']);
        }

        $fecha = \DateTime::createFromFormat('!Y-m-d', $valor);
        $errores = \DateTime::getLastErrors();

        if (!$fecha || ($errores !== false && ($errores['warning_count'] > 0 || $errores['error_count'] > 0))) {
            throw new HttpException('La fecha de fin debe tener el formato YYYY-MM-DD.', 400, ['field' => 'fecha_fin']);
        }

        if ($fecha <= new \DateTime()) {
            throw new HttpException('La fecha de fin debe ser futura.', 400, ['field' => 'fecha_fin']);
        }
    }

    private static function stringValue(array $data, string $key): string
    {
        if (!array_key_exists($key, $data) || $data[$key] === null || is_array($data[$key])) {
            return '';
        }

        return trim((string) $data[$key]);
    }
}
