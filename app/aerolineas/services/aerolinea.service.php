<?php

namespace App\Aerolineas\Services;

use App\Aerolineas\Dtos\CrearAerolineaDto;
use App\Aerolineas\Models\Aerolinea;
use App\Aerolineas\Repositories\AerolineaRepository;
use App\Paises\Services\PaisService;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../dtos/crear-aerolinea.dto.php';
require_once __DIR__ . '/../models/aerolinea.model.php';
require_once __DIR__ . '/../repositories/aerolinea.repository.php';
require_once __DIR__ . '/../../paises/services/pais.service.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class AerolineaService
{
    public function __construct(
        private AerolineaRepository $aerolineaRepository,
        private PaisService $paisService
    ) {
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

    public function crear(CrearAerolineaDto $dto): int
    {
        if ($this->aerolineaRepository->existsByCodigoIata($dto->codigoIata)) {
            throw new HttpException('Ya existe una aerolinea con ese codigo IATA.', 409, ['field' => 'codigoIata']);
        }

        if ($this->paisService === null || $this->paisService->getPorId($dto->paisId) === null) {
            throw new HttpException('El pais seleccionado no existe.', 400, ['field' => 'paisId']);
        }

        return $this->aerolineaRepository->crear($dto);
    }
}
