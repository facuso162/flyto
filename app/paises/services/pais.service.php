<?php

namespace App\Paises\Services;

use App\Paises\Models\Pais;
use App\Paises\Repositories\PaisRepository;

require_once __DIR__ . '/../models/pais.model.php';
require_once __DIR__ . '/../repositories/pais.repository.php';

class PaisService
{
    public function __construct(private PaisRepository $paisRepository)
    {
    }

    /**
     * @return Pais[]
     */
    public function getAll(): array
    {
        return $this->paisRepository->getAll();
    }

    public function getPorId(int $id): ?Pais
    {
        return $this->paisRepository->findById($id);
    }
}
