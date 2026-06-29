<?php

namespace App\Auth\Dtos;

class EnviarTokenRecuperacionDTO
{
    public string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }
}
