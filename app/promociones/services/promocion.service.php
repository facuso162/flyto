<?php

namespace App\Promociones\Services;

use App\Promociones\Dtos\CrearPromocionDto;
use App\Promociones\Dtos\EditarPromocionDto;
use App\Promociones\Models\Promocion;
use App\Promociones\Repositories\PromocionRepository;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../dtos/crear-promocion.dto.php';
require_once __DIR__ . '/../dtos/editar-promocion.dto.php';
require_once __DIR__ . '/../models/promocion.model.php';
require_once __DIR__ . '/../repositories/promocion.repository.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class PromocionService
{
    private PromocionRepository $promocionRepository;

    public function __construct(PromocionRepository $promocionRepository)
    {
        $this->promocionRepository = $promocionRepository;
    }

    public function crear(CrearPromocionDto $dto, int $aerolineaId): Promocion
    {
        $promocion = new Promocion(
            id: null,
            aerolineaId: $aerolineaId,
            descripcion: $dto->descripcion,
            descuento: $dto->descuento,
            fechaCreacion: new \DateTime(),
            fechaFin: $dto->fechaFin,
            fechaAprobacion: null,
            activa: false // Toda promoción nace inactiva hasta que el Admin la apruebe
        );

        $this->promocionRepository->create($promocion);
        return $promocion;
    }

    public function editar(EditarPromocionDto $dto): Promocion
    {
        $promocion = $this->promocionRepository->findById($dto->id);

        if (!$promocion) {
            throw new HttpException('Promoción no encontrada.', 404);
        }

        $promocion->descripcion = $dto->descripcion;
        $promocion->descuento = $dto->descuento;
        $promocion->fechaFin = $dto->fechaFin;
        
        // Si se edita, vuelve a estado pendiente de aprobación (Opcional según tu lógica de negocio)
        $promocion->activa = false; 
        $promocion->fechaAprobacion = null;

        $this->promocionRepository->update($promocion);
        return $promocion;
    }

    public function aprobar(int $id): Promocion
    {
        $promocion = $this->promocionRepository->findById($id);

        if (!$promocion) {
            throw new HttpException('Promoción no encontrada.', 404);
        }

        $promocion->activa = true;
        $promocion->fechaAprobacion = new \DateTime();

        $this->promocionRepository->update($promocion);
        return $promocion;
    }

    public function borrar(int $id): void
    {
        if (!$this->promocionRepository->findById($id)) {
            throw new HttpException('Promoción no encontrada.', 404);
        }
        $this->promocionRepository->delete($id);
    }

    public function getPendientes(): array
    {
        return $this->promocionRepository->getPendientes();
    }
}