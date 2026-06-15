<?php

namespace App\Novedades\Validators;

use App\Novedades\Dtos\CrearNovedadDto;
use App\Novedades\Dtos\EditarNovedadDto;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../dtos/crear-novedad.dto.php';
require_once __DIR__ . '/../dtos/editar-novedad.dto.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class NovedadValidator
{
    public static function crear(array $data): CrearNovedadDto
    {
        $payload = self::payload($data);

        return new CrearNovedadDto(
            $payload['titulo'],
            $payload['texto'],
            $payload['categoria'],
            $payload['fechaExpiracion']
        );
    }

    public static function editar(array $data): EditarNovedadDto
    {
        $payload = self::payload($data);

        return new EditarNovedadDto(
            self::id($data),
            $payload['titulo'],
            $payload['texto'],
            $payload['categoria'],
            $payload['fechaExpiracion']
        );
    }

    public static function borrar(array $data): int
    {
        return self::id($data);
    }

    private static function payload(array $data): array
    {
        $titulo = self::stringValue($data, 'titulo');
        $texto = self::stringValue($data, 'texto');
        $categoria = self::stringValue($data, 'categoria');
        $fechaExpiracion = self::dateValue($data, 'fechaExpiracion');

        if ($titulo === '') {
            throw new HttpException('El titulo es obligatorio.', 400, ['field' => 'titulo']);
        }

        if (strlen($titulo) > 160) {
            throw new HttpException('El titulo no puede superar los 160 caracteres.', 400, ['field' => 'titulo']);
        }

        if ($texto === '') {
            throw new HttpException('El texto es obligatorio.', 400, ['field' => 'texto']);
        }

        if (strlen($texto) > 2000) {
            throw new HttpException('El texto no puede superar los 2000 caracteres.', 400, ['field' => 'texto']);
        }

        if ($categoria === '') {
            throw new HttpException('La categoria es obligatoria.', 400, ['field' => 'categoria']);
        }

        if (strlen($categoria) > 120) {
            throw new HttpException('La categoria no puede superar los 120 caracteres.', 400, ['field' => 'categoria']);
        }

        if ($fechaExpiracion <= new \DateTime()) {
            throw new HttpException('La fecha de expiracion debe ser futura.', 400, ['field' => 'fechaExpiracion']);
        }

        return [
            'titulo' => $titulo,
            'texto' => $texto,
            'categoria' => $categoria,
            'fechaExpiracion' => $fechaExpiracion,
        ];
    }

    private static function id(array $data): int
    {
        $id = self::stringValue($data, 'id');

        if ($id === '' || filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new HttpException('El id de la novedad es obligatorio y debe ser numerico.', 400, ['field' => 'id']);
        }

        return (int) $id;
    }

    private static function dateValue(array $data, string $key): \DateTime
    {
        $value = self::stringValue($data, $key);

        if ($value === '') {
            throw new HttpException('La fecha de expiracion es obligatoria.', 400, ['field' => 'fechaExpiracion']);
        }

        try {
            return new \DateTime($value);
        } catch (\Throwable) {
            throw new HttpException('La fecha de expiracion no tiene un formato valido.', 400, ['field' => 'fechaExpiracion']);
        }
    }

    private static function stringValue(array $data, string $key): string
    {
        if (!array_key_exists($key, $data) || $data[$key] === null) {
            return '';
        }

        return trim((string) $data[$key]);
    }
}
