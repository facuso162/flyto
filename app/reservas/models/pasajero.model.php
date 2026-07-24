<?php

namespace App\Reservas\Models;

class Pasajero
{
    public ?int $id;
    public string $nombre;
    public string $apellido;
    public string $documento;
    public string $pasaporte;
    public \DateTime $fechaNacimiento;
    public string $nacionalidad;
    public string $telefonoContacto;
    public string $correoElectronico;

    public function __construct(
        ?int $id,
        string $nombre,
        string $apellido,
        string $documento,
        string $pasaporte,
        \DateTime $fechaNacimiento,
        string $nacionalidad,
        string $telefonoContacto,
        string $correoElectronico
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->documento = $documento;
        $this->pasaporte = $pasaporte;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->nacionalidad = $nacionalidad;
        $this->telefonoContacto = $telefonoContacto;
        $this->correoElectronico = $correoElectronico;
    }
}
