<?php

namespace App\Auth\Dtos;

class RegisterUsuarioDTO
{
    public string $email;
    public string $password;
    public string $nombre;
    public string $apellido;
    public ?string $telefono;

    public function __construct(
        string $email,
        string $password,
        string $nombre,
        string $apellido,
        ?string $telefono = null
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->telefono = $telefono;
    }
}
