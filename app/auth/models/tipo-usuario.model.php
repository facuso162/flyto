<?php

namespace App\Auth\Models;

class TipoUsuario
{
    public ?int $id;
    public string $nombre;

    public function __construct(
        ?int $id,
        string $nombre
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
    }
}
