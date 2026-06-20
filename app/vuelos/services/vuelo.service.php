<?php

namespace App\Vuelos\Services;

use App\Ciudades\Services\CiudadService;
use App\Vuelos\Dtos\BuscarVuelosDto;
use App\Vuelos\Dtos\CrearVueloDto;
use App\Vuelos\Models\Vuelo;
use App\Vuelos\Repositories\VueloRepository;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../ciudades/services/ciudad.service.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../dtos/buscar-vuelos.dto.php';
require_once __DIR__ . '/../dtos/crear-vuelo.dto.php';
require_once __DIR__ . '/../models/vuelo.model.php';
require_once __DIR__ . '/../repositories/vuelo.repository.php';

class VueloService
{
    public function __construct(
        private VueloRepository $vueloRepository,
        private CiudadService $ciudadService
    ) {
    }

    public function buscar(BuscarVuelosDto $dto): array
    {
        $base = $this->buscarBase($dto->sinFiltros());
        $precioMaximoDisponible = $this->precioMaximo($base);
        $precioMaximoSeleccionado = $dto->precioMaximo ?? $precioMaximoDisponible;
        $vuelos = $this->aplicarFiltros($base, $precioMaximoSeleccionado, $dto->aerolineas);
        $this->ordenar($vuelos, $dto->orden);

        return [
            'criterios' => $dto,
            'vuelos' => $vuelos,
            'aerolineas' => $this->aerolineasDisponibles($base),
            'precioMaximoDisponible' => $precioMaximoDisponible,
            'precioMaximoSeleccionado' => $precioMaximoSeleccionado,
            'total' => count($vuelos),
        ];
    }

    /**
     * @return Vuelo[]
     */
    public function getProximosByCeoId(int $ceoId, int $limite = 2): array
    {
        return $this->vueloRepository->getProximosByCeoId($ceoId, $limite);
    }

    public function getPaginatedByCeoId(
        int $ceoId,
        ?string $estado,
        int $pagina,
        int $porPagina = 3
    ): array {
        return $this->vueloRepository->getPaginatedByCeoId($ceoId, $estado, $pagina, $porPagina);
    }

    public function proponerCodigoByCeoId(int $ceoId): string
    {
        $aerolinea = $this->aerolineaDelCeo($ceoId);
        $prefijo = $this->iniciales((string) $aerolinea['nombre']);

        $numeroInicial = random_int(0, 999);
        for ($intento = 0; $intento < 1000; $intento++) {
            $numero = ($numeroInicial + $intento) % 1000;
            $codigo = $prefijo . str_pad((string) $numero, 3, '0', STR_PAD_LEFT);
            if (!$this->vueloRepository->existsByCodigo($codigo)) {
                return $codigo;
            }
        }

        throw new HttpException('No se pudo proponer un código de vuelo disponible.', 500);
    }

    public function crear(CrearVueloDto $dto, int $ceoId): int
    {
        $aerolinea = $this->aerolineaDelCeo($ceoId);

        if ($this->vueloRepository->existsByCodigo($dto->codigoVuelo)) {
            throw new HttpException('Ya existe un vuelo con ese código.', 409, ['field' => 'codigoVuelo']);
        }

        if ($this->ciudadService->getPorId($dto->origenCiudadId) === null) {
            throw new HttpException('La ciudad de origen no existe.', 400, ['field' => 'origenCiudadId']);
        }

        if ($this->ciudadService->getPorId($dto->destinoCiudadId) === null) {
            throw new HttpException('La ciudad de destino no existe.', 400, ['field' => 'destinoCiudadId']);
        }

        $estadoId = $this->vueloRepository->getEstadoIdByNombre('pendiente');
        if ($estadoId === null) {
            throw new HttpException('El estado inicial de vuelo no está configurado.', 500);
        }

        return $this->vueloRepository->crear($dto, (int) $aerolinea['id'], $estadoId);
    }

    private function aerolineaDelCeo(int $ceoId): array
    {
        $aerolinea = $this->vueloRepository->getAerolineaByCeoId($ceoId);
        if ($aerolinea === null) {
            throw new HttpException('El CEO no tiene una aerolínea asignada.', 404);
        }

        return $aerolinea;
    }

    private function iniciales(string $nombre): string
    {
        $normalizado = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nombre) ?: $nombre;
        $iniciales = preg_replace('/[^A-Za-z0-9]/', '', $normalizado) ?? '';

        return strtoupper(str_pad(substr($iniciales, 0, 3), 3, 'X'));
    }

    /**
     * @return Vuelo[]
     */
    private function buscarBase(BuscarVuelosDto $dto): array
    {
        return $this->vueloRepository->buscarDisponibles($dto);
    }

    /**
     * @param Vuelo[] $vuelos
     * @return Vuelo[]
     */
    private function aplicarFiltros(array $vuelos, ?int $precioMaximo, array $aerolineas): array
    {
        return array_values(array_filter($vuelos, function (Vuelo $vuelo) use ($precioMaximo, $aerolineas) {
            if ($precioMaximo !== null && $vuelo->precio > $precioMaximo) {
                return false;
            }

            if ($aerolineas !== [] && !in_array($vuelo->aerolinea['codigoIataAerolinea'], $aerolineas, true)) {
                return false;
            }

            return true;
        }));
    }

    /**
     * @param Vuelo[] $vuelos
     */
    private function ordenar(array &$vuelos, string $orden): void
    {
        usort($vuelos, function (Vuelo $a, Vuelo $b) use ($orden) {
            return match ($orden) {
                'duracion' => $a->duracionMinutos() <=> $b->duracionMinutos(),
                'salida' => $a->fechaSalida <=> $b->fechaSalida,
                default => $a->precio <=> $b->precio,
            };
        });
    }

    /**
     * @param Vuelo[] $vuelos
     */
    private function precioMaximo(array $vuelos): float
    {
        if ($vuelos === []) {
            return 0;
        }

        return max(array_map(fn (Vuelo $vuelo) => $vuelo->precio, $vuelos));
    }

    /**
     * @param Vuelo[] $vuelos
     */
    private function aerolineasDisponibles(array $vuelos): array
    {
        $aerolineas = [];

        foreach ($vuelos as $vuelo) {
            $codigoIata = $vuelo->aerolinea['codigoIataAerolinea'];
            $aerolineas[$codigoIata] = [
                'id' => $vuelo->aerolinea['idAerolinea'],
                'nombre' => $vuelo->aerolinea['nombreAerolinea'],
                'codigoIata' => $codigoIata,
            ];
        }

        uasort($aerolineas, fn (array $a, array $b) => $a['nombre'] <=> $b['nombre']);

        return array_values($aerolineas);
    }
}
