<?php

namespace App\Vuelos\Validators;

require_once __DIR__ . '/crear-vuelo.validator.php';

class EditarVueloValidator
{
    public static function validate(array $data): void
    {
        CrearVueloValidator::validate($data);
    }
}
