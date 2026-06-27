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

    /**
     * @return Aerolinea[]
     */
    public function getSinCeo(): array
    {
        return $this->aerolineaRepository->getSinCeo();
    }

    public function getPorCeoId(int $ceoId): ?Aerolinea
    {
        return $this->aerolineaRepository->getPorCeoId($ceoId);
    }

    public function getPorId(int $id): Aerolinea
    {
        $aerolinea = $this->aerolineaRepository->getPorId($id);

        if ($aerolinea === null) {
            throw new HttpException('La aerolinea solicitada no existe.', 404);
        }

        return $aerolinea;
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

    public function editar(int $id, CrearAerolineaDto $dto): void
    {
        $this->getPorId($id);

        if ($this->aerolineaRepository->existsByCodigoIataExcludingId($dto->codigoIata, $id)) {
            throw new HttpException('Ya existe una aerolinea con ese codigo IATA.', 409, ['field' => 'codigoIata']);
        }

        if ($this->paisService === null || $this->paisService->getPorId($dto->paisId) === null) {
            throw new HttpException('El pais seleccionado no existe.', 400, ['field' => 'paisId']);
        }

        $this->aerolineaRepository->editar($id, $dto);
    }

    public function borrar(Aerolinea $aerolinea): void
    {
        if ($aerolinea->ceo !== null) {
            throw new HttpException('La aerolinea no se puede borrar porque tiene un CEO asignado.', 409);
        }

        $this->aerolineaRepository->borrar($aerolinea->id);
    }
}
