<?php

namespace App\Usuarios\Models;

class Usuario
{
    public function __construct(
        public ?int $id,
        public string $nombre,
        public string $apellido,
        public string $email,
        public string $tipo,
        public bool $activo,
        public \DateTime $fechaRegistro,
        public bool $emailVerificado,
        public ?array $aerolinea = null
    ) {
    }

    public function nombreCompleto(): string
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }
}
