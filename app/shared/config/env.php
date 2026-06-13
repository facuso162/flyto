<?php

namespace App\Shared\Config;

use App\Shared\Http\HttpException;

class Env
{
    public static function load(string $path): void
    {
        if (!is_file($path)) {
            return;
        }

        $values = parse_ini_file($path, false, INI_SCANNER_RAW);

        if (!is_array($values)) {
            return;
        }

        foreach ($values as $key => $value) {
            if (getenv($key) !== false) {
                continue;
            }

            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    public static function env(string $key, ?string $default = null): string
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            if ($default !== null) {
                return $default;
            }

            throw new HttpException("Falta configurar {$key}.", 500);
        }

        return $value;
    }
}
