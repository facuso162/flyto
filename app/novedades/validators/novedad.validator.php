<?php

namespace App\Novedades\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class NovedadValidator
{
    public static function crear(array $data): void
    {
        self::payload($data);
    }

    public static function editar(array $data): void
    {
        self::id($data);
        self::payload($data);
    }

    public static function borrar(array $data): void
    {
        self::id($data);
    }

    private static function payload(array $data): void
    {
        $titulo = self::stringValue($data, 'titulo');
        $texto = self::stringValue($data, 'texto');
        $categoria = self::stringValue($data, 'categoria');
        $fechaExpiracion = self::dateValue($data, 'fechaExpiracion');

        if ($titulo === '') {
            throw new HttpException('El titulo es obligatorio.', 400, ['field' => 'titulo']);
        }

        if (self::length($titulo) > 100) {
            throw new HttpException('El titulo no puede superar los 100 caracteres.', 400, ['field' => 'titulo']);
        }

        if ($texto === '') {
            throw new HttpException('El texto es obligatorio.', 400, ['field' => 'texto']);
        }

        if (self::length($texto) > 200) {
            throw new HttpException('El texto no puede superar los 200 caracteres.', 400, ['field' => 'texto']);
        }

        if ($categoria === '') {
            throw new HttpException('La categoria es obligatoria.', 400, ['field' => 'categoria']);
        }

        if (self::length($categoria) > 50) {
            throw new HttpException('La categoria no puede superar los 50 caracteres.', 400, ['field' => 'categoria']);
        }

        if ($fechaExpiracion <= new \DateTime('today')) {
            throw new HttpException('La fecha de expiracion debe ser futura.', 400, ['field' => 'fechaExpiracion']);
        }
    }

    private static function id(array $data): void
    {
        $id = self::stringValue($data, 'id');

        if ($id === '' || filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new HttpException('El id de la novedad es obligatorio y debe ser numerico.', 400, ['field' => 'id']);
        }
    }

    private static function dateValue(array $data, string $key): \DateTime
    {
        $value = self::stringValue($data, $key);

        if ($value === '') {
            throw new HttpException('La fecha de expiracion es obligatoria.', 400, ['field' => 'fechaExpiracion']);
        }

        $fecha = \DateTime::createFromFormat('!Y-m-d', $value);
        $errores = \DateTime::getLastErrors();

        if (!$fecha || ($errores !== false && ($errores['warning_count'] > 0 || $errores['error_count'] > 0))) {
            throw new HttpException('La fecha de expiracion debe tener el formato YYYY-MM-DD.', 400, ['field' => 'fechaExpiracion']);
        }

        return $fecha;
    }

    private static function stringValue(array $data, string $key): string
    {
        if (!array_key_exists($key, $data) || $data[$key] === null || is_array($data[$key])) {
            return '';
        }

        return trim((string) $data[$key]);
    }

    private static function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }
}
