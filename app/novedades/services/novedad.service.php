<?php

namespace App\Novedades\Services;

use App\Novedades\Dtos\CrearNovedadDto;
use App\Novedades\Dtos\EditarNovedadDto;
use App\Novedades\Models\Novedad;
use App\Novedades\Repositories\NovedadRepository;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../dtos/crear-novedad.dto.php';
require_once __DIR__ . '/../dtos/editar-novedad.dto.php';
require_once __DIR__ . '/../models/novedad.model.php';
require_once __DIR__ . '/../repositories/novedad.repository.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class NovedadService
{
    private NovedadRepository $novedadRepository;

    public function __construct(NovedadRepository $novedadRepository)
    {
        $this->novedadRepository = $novedadRepository;
    }

    public function crear(CrearNovedadDto $dto): Novedad
    {
        $novedad = new Novedad(
            id: null,
            titulo: $dto->titulo,
            texto: $dto->texto,
            categoria: $dto->categoria,
            fechaPublicacion: new \DateTime(),
            fechaExpiracion: $dto->fechaExpiracion
        );

        $this->novedadRepository->create($novedad);

        return $novedad;
    }

    public function editar(EditarNovedadDto $dto): Novedad
    {
        $novedad = $this->getById($dto->id);

        $novedad->titulo = $dto->titulo;
        $novedad->texto = $dto->texto;
        $novedad->categoria = $dto->categoria;
        $novedad->fechaExpiracion = $dto->fechaExpiracion;

        $this->novedadRepository->update($novedad);

        return $novedad;
    }

    public function getById(int $id): Novedad
    {
        $novedad = $this->novedadRepository->findById($id);

        if (!$novedad) {
            throw new HttpException('Novedad no encontrada.', 404);
        }

        return $novedad;
    }

    public function borrar(int $id): void
    {
        $this->getById($id);
        $this->novedadRepository->delete($id);
    }

    public function getUltimas(): array
    {
        return $this->novedadRepository->getUltimas();
    }

    public function getVigentes(): array
    {
        return $this->novedadRepository->getVigentes();
    }

    public function getTodas(): array
    {
        return $this->novedadRepository->getTodas();
    }
}
