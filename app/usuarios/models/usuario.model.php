<?php

namespace App\Usuarios\Models;

use App\Aerolineas\Models\Aerolinea;

require_once __DIR__ . '/../../aerolineas/models/aerolinea.model.php';

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
        public ?Aerolinea $aerolinea = null
    ) {
    }

    public function nombreCompleto(): string
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }
}
