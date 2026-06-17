<?php

namespace App\Promociones\Validators;

use App\Promociones\Dtos\CrearPromocionDto;
use App\Promociones\Dtos\EditarPromocionDto;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../dtos/crear-promocion.dto.php';
require_once __DIR__ . '/../dtos/editar-promocion.dto.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class PromocionValidator
{
    public static function crear(array $data): CrearPromocionDto
    {
        $payload = self::payload($data);
        return new CrearPromocionDto($payload['descripcion'], $payload['descuento'], $payload['fechaFin']);
    }

    public static function editar(array $data): EditarPromocionDto
    {
        $payload = self::payload($data);
        return new EditarPromocionDto(self::id($data), $payload['descripcion'], $payload['descuento'], $payload['fechaFin']);
    }

    public static function borrar(array $data): int
    {
        return self::id($data);
    }

    public static function aprobar(array $data): int
    {
        return self::id($data);
    }

    private static function payload(array $data): array
    {
        $descripcion = self::stringValue($data, 'descripcion');
        $descuento = self::floatValue($data, 'descuento');
        $fechaFin = self::dateValue($data, 'fechaFin');

        if ($descripcion === '') {
            throw new HttpException('La descripción es obligatoria.', 400, ['field' => 'descripcion']);
        }

        if (strlen($descripcion) > 255) {
            throw new HttpException('La descripción no puede superar los 255 caracteres.', 400, ['field' => 'descripcion']);
        }

        if ($descuento <= 0 || $descuento > 100) {
            throw new HttpException('El descuento debe ser un porcentaje entre 1 y 100.', 400, ['field' => 'descuento']);
        }

        if ($fechaFin <= new \DateTime()) {
            throw new HttpException('La fecha de fin debe ser futura.', 400, ['field' => 'fechaFin']);
        }

        return [
            'descripcion' => $descripcion,
            'descuento' => $descuento,
            'fechaFin' => $fechaFin,
        ];
    }

    private static function id(array $data): int
    {
        $id = self::stringValue($data, 'id');
        if ($id === '' || filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new HttpException('El id de la promoción es obligatorio y debe ser numérico.', 400, ['field' => 'id']);
        }
        return (int) $id;
    }

    private static function stringValue(array $data, string $key): string
    {
        if (!array_key_exists($key, $data) || $data[$key] === null) { return ''; }
        return trim((string) $data[$key]);
    }

    private static function floatValue(array $data, string $key): float
    {
        if (!array_key_exists($key, $data) || $data[$key] === null) { return 0; }
        return (float) $data[$key];
    }

    private static function dateValue(array $data, string $key): \DateTime
    {
        $value = self::stringValue($data, $key);
        if ($value === '') {
            throw new HttpException('La fecha de fin es obligatoria.', 400, ['field' => 'fechaFin']);
        }
        try {
            return new \DateTime($value);
        } catch (\Throwable) {
            throw new HttpException('La fecha de fin no tiene un formato válido.', 400, ['field' => 'fechaFin']);
        }
    }
}