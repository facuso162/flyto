<?php

namespace App\Aerolineas\Services;

use App\Aerolineas\Models\Aerolinea;
use App\Aerolineas\Repositories\AerolineaRepository;

require_once __DIR__ . '/../models/aerolinea.model.php';
require_once __DIR__ . '/../repositories/aerolinea.repository.php';

class AerolineaService
{
    public function __construct(private AerolineaRepository $aerolineaRepository)
    {
    }

    /**
     * @return Aerolinea[]
     */
    public function getTodas(): array
    {
        return $this->aerolineaRepository->getTodas();
    }

    public function getPorCeoId(int $ceoId): ?Aerolinea
    {
        return $this->aerolineaRepository->getPorCeoId($ceoId);
    }
}
