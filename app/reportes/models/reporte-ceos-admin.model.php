<?php

namespace App\Reportes\Models;

class CeoDelMes
{
    public function __construct(
        public int $id,
        public string $nombre,
        public string $apellido,
        public ?string $aerolinea,
        public int $reservas,
        public float $ingresos
    ) {
    }
}

class ReporteCeosAdmin
{
    public function __construct(
        public \DateTimeImmutable $periodo,
        public \DateTimeImmutable $generadoEn,
        public int $totalCeos,
        public int $ceosNuevosMes,
        public int $ceosConPromocionActiva,
        public ?CeoDelMes $ceoDelMes
    ) {
    }
}
