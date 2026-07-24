<?php

namespace App\Vuelos\Dtos;

class BuscarVuelosDto
{
    public int $origen;
    public ?int $destino;
    public ?string $fechaSalida;
    public int $cantidadPasajeros;
    public ?int $precioMaximo;
    /** @var string[] */
    public array $aerolineas;
    public string $orden;

    public function __construct(
        int $origen,
        ?int $destino,
        ?string $fechaSalida,
        int $cantidadPasajeros,
        ?int $precioMaximo = null,
        array $aerolineas = [],
        string $orden = 'precio'
    ) {
        $this->origen = $origen;
        $this->destino = $destino;
        $this->fechaSalida = $fechaSalida;
        $this->cantidadPasajeros = $cantidadPasajeros;
        $this->precioMaximo = $precioMaximo;
        $this->aerolineas = $aerolineas;
        $this->orden = $orden;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            origen: (int) $data['origen'],
            destino: isset($data['destino']) && trim((string) $data['destino']) !== ''
                ? (int) $data['destino']
                : null,
            fechaSalida: isset($data['fechaSalida']) && trim((string) $data['fechaSalida']) !== ''
                ? (string) $data['fechaSalida']
                : null,
            cantidadPasajeros: (int) $data['cantidadPasajeros'],
            precioMaximo: isset($data['precioMaximo']) && trim((string) $data['precioMaximo']) !== ''
                ? (int) $data['precioMaximo']
                : null,
            aerolineas: self::parseAerolineas($data['aerolineas'] ?? []),
            orden: (string) ($data['orden'] ?? 'precio')
        );
    }

    public function sinFiltros(): self
    {
        return new self(
            origen: $this->origen,
            destino: $this->destino,
            fechaSalida: $this->fechaSalida,
            cantidadPasajeros: $this->cantidadPasajeros,
            precioMaximo: null,
            aerolineas: [],
            orden: $this->orden
        );
    }

    /**
     * @return string[]
     */
    private static function parseAerolineas(mixed $aerolineas): array
    {
        if (is_array($aerolineas)) {
            return array_values(array_map(static fn ($codigo): string => (string) $codigo, $aerolineas));
        }

        return $aerolineas === '' ? [] : explode(',', (string) $aerolineas);
    }

}
