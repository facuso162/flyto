<?php

namespace App\Auth\Dtos;

class VerificarTokenRecuperacionDTO
{
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}
