<?php

namespace App\Promociones\Services;

use App\Promociones\Dtos\ActivarPromocionDto;
use App\Promociones\Dtos\CrearPromocionDto;
use App\Promociones\Dtos\EditarPromocionDto;
use App\Promociones\Models\Promocion;
use App\Promociones\Repositories\PromocionRepository;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../dtos/activar-promocion.dto.php';
require_once __DIR__ . '/../dtos/crear-promocion.dto.php';
require_once __DIR__ . '/../dtos/editar-promocion.dto.php';
require_once __DIR__ . '/../models/promocion.model.php';
require_once __DIR__ . '/../repositories/promocion.repository.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class PromocionService
{
    private const ACTIVA = 'activa';
    private const INACTIVA = 'inactiva';
    private const PENDIENTE_ACTIVACION = 'pendiente_activacion';

    private PromocionRepository $promocionRepository;

    public function __construct(PromocionRepository $promocionRepository)
    {
        $this->promocionRepository = $promocionRepository;
    }

    public function crear(CrearPromocionDto $dto, int $ceoId): Promocion
    {
        $promocion = new Promocion(
            id: null,
            descripcion: $dto->descripcion,
            descuento: $dto->descuento / 100,
            fechaCreacion: new \DateTime(),
            fechaAprobacion: null,
            fechaFin: null,
            estado: $this->estado(self::INACTIVA),
            aerolinea: $this->aerolineaDelCeo($ceoId),
            activa: true
        );

        $this->promocionRepository->create($promocion);

        return $promocion;
    }

    public function editar(EditarPromocionDto $dto, int $ceoId): Promocion
    {
        $promocion = $this->getEditableByCeoId($dto->id, $ceoId);
        $estadoAnterior = (string) ($promocion->estado['descripcion'] ?? '');

        $promocion->descripcion = $dto->descripcion;
        $promocion->descuento = $dto->descuento / 100;

        if (in_array($estadoAnterior, [self::ACTIVA, self::PENDIENTE_ACTIVACION], true)) {
            $promocion->estado = $this->estado(self::INACTIVA);
            $promocion->fechaFin = null;
            $promocion->fechaAprobacion = null;

            $this->promocionRepository->updateEditableFieldsAndDeactivate($promocion);
        } else {
            $this->promocionRepository->updateEditableFields(
                $dto->id,
                $promocion->descripcion,
                $promocion->descuento
            );
        }

        return $promocion;
    }

    public function borrar(int $id): void
    {
        $this->promocion($id);
        $this->promocionRepository->delete($id);
    }

    public function solicitarActivacion(ActivarPromocionDto $dto): Promocion
    {
        $promocion = $this->promocion($dto->id);
        $estadoActiva = $this->estado(self::ACTIVA);
        $estadoInactiva = $this->estado(self::INACTIVA);
        $estadoPendiente = $this->estado(self::PENDIENTE_ACTIVACION);

        $promocion->estado = $estadoPendiente;
        $promocion->fechaFin = $dto->fechaFin;
        $estadosExclusivosIds = [(int) $estadoActiva['id'], (int) $estadoPendiente['id']];

        $this->promocionRepository->requestActivation(
            $promocion,
            (int) $estadoInactiva['id'],
            $estadosExclusivosIds
        );

        return $promocion;
    }

    public function desactivar(int $id): Promocion
    {
        $promocion = $this->promocion($id);
        $promocion->estado = $this->estado(self::INACTIVA);
        $this->promocionRepository->update($promocion);

        return $promocion;
    }

    public function aprobar(int $id): Promocion
    {
        $promocion = $this->promocion($id);
        $promocion->estado = $this->estado(self::ACTIVA);
        $promocion->fechaAprobacion = new \DateTime();
        $this->promocionRepository->update($promocion);

        return $promocion;
    }

    public function denegar(int $id): Promocion
    {
        $promocion = $this->promocion($id);
        $promocion->estado = $this->estado(self::INACTIVA);
        $promocion->fechaFin = null;
        $this->promocionRepository->update($promocion);

        return $promocion;
    }

    public function getAll(): array
    {
        return $this->promocionRepository->getAll();
    }

    public function getById(int $id): Promocion
    {
        return $this->promocion($id);
    }

    public function getEditableByCeoId(int $id, int $ceoId): Promocion
    {
        $promocion = $this->promocion($id);
        $aerolinea = $this->aerolineaDelCeo($ceoId);

        if ((int) ($promocion->aerolinea['id'] ?? 0) !== (int) $aerolinea['id']) {
            throw new HttpException('No podés editar promociones de otra aerolínea.', 403);
        }

        $estado = (string) ($promocion->estado['descripcion'] ?? '');

        return $promocion;
    }

    public function getByEstado(string $estado): array
    {
        return $this->promocionRepository->getByEstado($estado);
    }

    public function getByCeoId(int $ceoId): array
    {
        return $this->promocionRepository->getByCeoId($ceoId);
    }

    public function getActivaByCeoId(int $ceoId): ?Promocion
    {
        return $this->promocionRepository->getActivaByCeoId($ceoId);
    }

    private function promocion(int $id): Promocion
    {
        $promocion = $this->promocionRepository->getById($id);

        if (!$promocion) {
            throw new HttpException('Promocion no encontrada.', 404);
        }

        return $promocion;
    }

    private function estado(string $descripcion): array
    {
        $estado = $this->promocionRepository->getEstadoByDescripcion($descripcion);

        if (!$estado) {
            throw new HttpException('El estado de promocion no esta configurado.', 500);
        }

        return $estado;
    }

    private function aerolineaDelCeo(int $ceoId): array
    {
        $aerolinea = $this->promocionRepository->getAerolineaByCeoId($ceoId);

        if (!$aerolinea) {
            throw new HttpException('El CEO no tiene una aerolinea asignada.', 404);
        }

        return $aerolinea;
    }
}
