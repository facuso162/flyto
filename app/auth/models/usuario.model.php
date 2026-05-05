<?php

namespace App\Auth\Models;

use App\Auth\Models\TipoUsuario;

require_once __DIR__ . '/tipo-usuario.model.php';

class Usuario
{
    public ?int $id;
    public string $nombre;
    public string $apellido;
    public string $email;
    public ?string $telefono;
    public string $claveHash;
    public TipoUsuario $tipoUsuario;
    public bool $activo;
    public \DateTime $fechaRegistro;
    public bool $emailVerificado;
    public ?string $tokenVerificacion;
    public ?string $tokenRecupero;
    public ?\DateTime $tokenExpiracion;

    public function __construct(
        ?int $id,
        string $nombre,
        string $apellido,
        string $email,
        ?string $telefono,
        string $claveHash,
        TipoUsuario $tipoUsuario,
        bool $activo,
        \DateTime $fechaRegistro,
        bool $emailVerificado,
        ?string $tokenVerificacion,
        ?string $tokenRecupero,
        ?\DateTime $tokenExpiracion
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->claveHash = $claveHash;
        $this->tipoUsuario = $tipoUsuario;
        $this->activo = $activo;
        $this->fechaRegistro = $fechaRegistro;
        $this->emailVerificado = $emailVerificado;
        $this->tokenVerificacion = $tokenVerificacion;
        $this->tokenRecupero = $tokenRecupero;
        $this->tokenExpiracion = $tokenExpiracion;
    }

    public function activar(): void {
        $this->activo = true;
        $this->emailVerificado = true;
        $this->tokenVerificacion = null;
    }
}
