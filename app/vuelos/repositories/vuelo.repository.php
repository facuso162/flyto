<?php

namespace App\Vuelos\Repositories;

use App\Aerolineas\Models\Aerolinea;
use App\Vuelos\Dtos\BuscarVuelosDto;
use App\Vuelos\Dtos\CrearVueloDto;
use App\Vuelos\Dtos\EditarVueloDto;
use App\Vuelos\Models\Vuelo;
use PDO;

require_once __DIR__ . '/../dtos/buscar-vuelos.dto.php';
require_once __DIR__ . '/../dtos/crear-vuelo.dto.php';
require_once __DIR__ . '/../dtos/editar-vuelo.dto.php';
require_once __DIR__ . '/../models/vuelo.model.php';
require_once __DIR__ . '/../../aerolineas/models/aerolinea.model.php';

class VueloRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return Vuelo[]
     */
    public function buscarDisponibles(BuscarVuelosDto $dto): array
    {
        $sql = "
            SELECT
                v.id,
                v.codigoVuelo,
                v.aerolinea_id,
                a.nombre AS aerolinea_nombre,
                a.codigo_iata AS aerolinea_codigo_iata,
                a.descripcion AS aerolinea_descripcion,
                a.pais_id AS aerolinea_pais_id,
                aerolinea_pais.nombre AS aerolinea_pais_nombre,
                aerolinea_pais.codigo AS aerolinea_pais_codigo,
                a.ceo_id AS aerolinea_ceo_id,
                aerolinea_ceo.nombre AS aerolinea_ceo_nombre,
                aerolinea_ceo.apellido AS aerolinea_ceo_apellido,
                a.activa AS aerolinea_activa,
                promocion.id AS promocion_id,
                promocion.descripcion AS promocion_descripcion,
                promocion.descuento AS promocion_descuento,
                promocion.fecha_creacion AS promocion_fecha_creacion,
                promocion.fecha_aprobacion AS promocion_fecha_aprobacion,
                promocion.fecha_fin AS promocion_fecha_fin,
                promocion.estado AS promocion_estado,
                v.origen_ciudad_id,
                origen.nombre AS origen_nombre,
                origen.abreviacion AS origen_abreviacion,
                origen_pais.id AS origen_pais_id,
                origen_pais.nombre AS origen_pais_nombre,
                v.destino_ciudad_id,
                destino.nombre AS destino_nombre,
                destino.abreviacion AS destino_abreviacion,
                destino_pais.id AS destino_pais_id,
                destino_pais.nombre AS destino_pais_nombre,
                v.precio,
                v.asientos_disponibles,
                v.fecha_salida,
                v.fecha_llegada,
                v.fecha_creacion,
                v.distancia_km,
                v.duracion_horas,
                ev.nombre AS estado,
                v.asientosOcupados AS asientos_ocupados
            FROM vuelos v
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
            INNER JOIN paises aerolinea_pais ON aerolinea_pais.id = a.pais_id
            LEFT JOIN usuarios aerolinea_ceo ON aerolinea_ceo.id = a.ceo_id
            LEFT JOIN (
                SELECT
                    p.id,
                    p.descripcion,
                    p.descuento,
                    p.fecha_creacion,
                    p.fecha_aprobacion,
                    p.fecha_fin,
                    p.aerolinea_id,
                    ep.nombre AS estado
                FROM promociones p
                INNER JOIN estados_promociones ep ON ep.id = p.estado_id
                WHERE LOWER(ep.nombre) = 'activa'
            ) promocion ON promocion.aerolinea_id = a.id
            INNER JOIN ciudades origen ON origen.id = v.origen_ciudad_id
            INNER JOIN paises origen_pais ON origen_pais.id = origen.pais_id
            INNER JOIN ciudades destino ON destino.id = v.destino_ciudad_id
            INNER JOIN paises destino_pais ON destino_pais.id = destino.pais_id
            INNER JOIN estados_vuelos ev ON ev.id = v.estado_id
            WHERE v.origen_ciudad_id = :origen
                AND v.destino_ciudad_id = :destino
                AND DATE(v.fecha_salida) = :fechaSalida
                AND LOWER(ev.nombre) = 'pendiente'
            GROUP BY
                v.id,
                v.codigoVuelo,
                v.aerolinea_id,
                a.nombre,
                a.codigo_iata,
                a.descripcion,
                a.pais_id,
                aerolinea_pais.nombre,
                aerolinea_pais.codigo,
                a.ceo_id,
                aerolinea_ceo.nombre,
                aerolinea_ceo.apellido,
                a.activa,
                promocion.id,
                promocion.descripcion,
                promocion.descuento,
                promocion.fecha_creacion,
                promocion.fecha_aprobacion,
                promocion.fecha_fin,
                promocion.estado,
                v.origen_ciudad_id,
                origen.nombre,
                origen.abreviacion,
                origen_pais.id,
                origen_pais.nombre,
                v.destino_ciudad_id,
                destino.nombre,
                destino.abreviacion,
                destino_pais.id,
                destino_pais.nombre,
                v.precio,
                v.asientos_disponibles,
                v.fecha_salida,
                v.fecha_llegada,
                v.fecha_creacion,
                v.distancia_km,
                v.duracion_horas,
                ev.nombre
            HAVING (v.asientos_disponibles - asientos_ocupados) >= :cantidadPasajeros
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':origen' => $dto->origen,
            ':destino' => $dto->destino,
            ':fechaSalida' => $dto->fechaSalida,
            ':cantidadPasajeros' => $dto->cantidadPasajeros,
        ]);

        return array_map(fn (array $row) => $this->mapRow($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return Vuelo[]
     */
    public function getProximosByCeoId(int $ceoId, int $limite = 2): array
    {
        $sql = "
            SELECT
                v.id,
                v.codigoVuelo,
                v.aerolinea_id,
                a.nombre AS aerolinea_nombre,
                a.codigo_iata AS aerolinea_codigo_iata,
                a.descripcion AS aerolinea_descripcion,
                a.pais_id AS aerolinea_pais_id,
                aerolinea_pais.nombre AS aerolinea_pais_nombre,
                aerolinea_pais.codigo AS aerolinea_pais_codigo,
                a.ceo_id AS aerolinea_ceo_id,
                aerolinea_ceo.nombre AS aerolinea_ceo_nombre,
                aerolinea_ceo.apellido AS aerolinea_ceo_apellido,
                a.activa AS aerolinea_activa,
                promocion.id AS promocion_id,
                promocion.descripcion AS promocion_descripcion,
                promocion.descuento AS promocion_descuento,
                promocion.fecha_creacion AS promocion_fecha_creacion,
                promocion.fecha_aprobacion AS promocion_fecha_aprobacion,
                promocion.fecha_fin AS promocion_fecha_fin,
                promocion.estado AS promocion_estado,
                v.origen_ciudad_id,
                origen.nombre AS origen_nombre,
                origen.abreviacion AS origen_abreviacion,
                origen_pais.id AS origen_pais_id,
                origen_pais.nombre AS origen_pais_nombre,
                v.destino_ciudad_id,
                destino.nombre AS destino_nombre,
                destino.abreviacion AS destino_abreviacion,
                destino_pais.id AS destino_pais_id,
                destino_pais.nombre AS destino_pais_nombre,
                v.precio,
                v.asientos_disponibles,
                v.asientosOcupados AS asientos_ocupados,
                v.fecha_salida,
                v.fecha_llegada,
                v.fecha_creacion,
                v.distancia_km,
                v.duracion_horas,
                ev.nombre AS estado
            FROM vuelos v
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
            INNER JOIN paises aerolinea_pais ON aerolinea_pais.id = a.pais_id
            LEFT JOIN usuarios aerolinea_ceo ON aerolinea_ceo.id = a.ceo_id
            INNER JOIN ciudades origen ON origen.id = v.origen_ciudad_id
            INNER JOIN paises origen_pais ON origen_pais.id = origen.pais_id
            INNER JOIN ciudades destino ON destino.id = v.destino_ciudad_id
            INNER JOIN paises destino_pais ON destino_pais.id = destino.pais_id
            INNER JOIN estados_vuelos ev ON ev.id = v.estado_id
            LEFT JOIN (
                SELECT p.id, p.descripcion, p.descuento, p.fecha_creacion,
                       p.fecha_aprobacion, p.fecha_fin, p.aerolinea_id,
                       ep.nombre AS estado
                FROM promociones p
                INNER JOIN estados_promociones ep ON ep.id = p.estado_id
                WHERE LOWER(ep.nombre) = 'activa' AND p.activa = 1
            ) promocion ON promocion.aerolinea_id = a.id
            WHERE a.ceo_id = :ceo_id
                AND v.fecha_salida >= NOW()
                AND LOWER(ev.nombre) = 'pendiente'
            ORDER BY v.fecha_salida ASC
            LIMIT :limite
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':ceo_id', $ceoId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', max(1, $limite), PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn (array $row) => $this->mapRow($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getPaginatedByCeoId(
        int $ceoId,
        ?string $estado,
        int $pagina,
        int $porPagina = 3
    ): array {
        $whereEstado = $estado !== null ? ' AND LOWER(ev.nombre) = :estado' : '';
        $parametros = [':ceo_id' => $ceoId];

        if ($estado !== null) {
            $parametros[':estado'] = $estado;
        }

        $countSql = "
            SELECT COUNT(*)
            FROM vuelos v
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
            INNER JOIN estados_vuelos ev ON ev.id = v.estado_id
            WHERE a.ceo_id = :ceo_id{$whereEstado}
        ";
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($parametros);
        $total = (int) $countStmt->fetchColumn();

        $porPagina = max(1, $porPagina);
        $totalPaginas = max(1, (int) ceil($total / $porPagina));
        $pagina = min(max(1, $pagina), $totalPaginas);
        $offset = ($pagina - 1) * $porPagina;

        $sql = "
            SELECT
                v.id,
                v.codigoVuelo,
                v.aerolinea_id,
                a.nombre AS aerolinea_nombre,
                a.codigo_iata AS aerolinea_codigo_iata,
                a.descripcion AS aerolinea_descripcion,
                a.pais_id AS aerolinea_pais_id,
                aerolinea_pais.nombre AS aerolinea_pais_nombre,
                aerolinea_pais.codigo AS aerolinea_pais_codigo,
                a.ceo_id AS aerolinea_ceo_id,
                aerolinea_ceo.nombre AS aerolinea_ceo_nombre,
                aerolinea_ceo.apellido AS aerolinea_ceo_apellido,
                a.activa AS aerolinea_activa,
                NULL AS promocion_id,
                NULL AS promocion_descripcion,
                NULL AS promocion_descuento,
                NULL AS promocion_fecha_creacion,
                NULL AS promocion_fecha_aprobacion,
                NULL AS promocion_fecha_fin,
                NULL AS promocion_estado,
                v.origen_ciudad_id,
                origen.nombre AS origen_nombre,
                origen.abreviacion AS origen_abreviacion,
                origen_pais.id AS origen_pais_id,
                origen_pais.nombre AS origen_pais_nombre,
                v.destino_ciudad_id,
                destino.nombre AS destino_nombre,
                destino.abreviacion AS destino_abreviacion,
                destino_pais.id AS destino_pais_id,
                destino_pais.nombre AS destino_pais_nombre,
                v.precio,
                v.asientos_disponibles,
                v.asientosOcupados AS asientos_ocupados,
                v.fecha_salida,
                v.fecha_llegada,
                v.fecha_creacion,
                v.distancia_km,
                v.duracion_horas,
                ev.nombre AS estado
            FROM vuelos v
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
            INNER JOIN paises aerolinea_pais ON aerolinea_pais.id = a.pais_id
            LEFT JOIN usuarios aerolinea_ceo ON aerolinea_ceo.id = a.ceo_id
            INNER JOIN ciudades origen ON origen.id = v.origen_ciudad_id
            INNER JOIN paises origen_pais ON origen_pais.id = origen.pais_id
            INNER JOIN ciudades destino ON destino.id = v.destino_ciudad_id
            INNER JOIN paises destino_pais ON destino_pais.id = destino.pais_id
            INNER JOIN estados_vuelos ev ON ev.id = v.estado_id
            WHERE a.ceo_id = :ceo_id{$whereEstado}
            ORDER BY v.fecha_salida DESC, v.id DESC
            LIMIT :limite OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':ceo_id', $ceoId, PDO::PARAM_INT);
        if ($estado !== null) {
            $stmt->bindValue(':estado', $estado);
        }
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'vuelos' => array_map(fn (array $row) => $this->mapRow($row), $stmt->fetchAll(PDO::FETCH_ASSOC)),
            'total' => $total,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
        ];
    }

    public function existsByCodigo(string $codigo): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM vuelos WHERE codigoVuelo = :codigo LIMIT 1');
        $stmt->execute([':codigo' => $codigo]);

        return $stmt->fetchColumn() !== false;
    }

    public function existsByCodigoExcludingId(string $codigo, int $vueloId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM vuelos WHERE codigoVuelo = :codigo AND id <> :id LIMIT 1'
        );
        $stmt->execute([':codigo' => $codigo, ':id' => $vueloId]);

        return $stmt->fetchColumn() !== false;
    }

    public function getById(int $vueloId): ?Vuelo
    {
        $stmt = $this->pdo->prepare("
            SELECT
                v.id,
                v.codigoVuelo,
                v.aerolinea_id,
                a.nombre AS aerolinea_nombre,
                a.codigo_iata AS aerolinea_codigo_iata,
                a.descripcion AS aerolinea_descripcion,
                a.pais_id AS aerolinea_pais_id,
                aerolinea_pais.nombre AS aerolinea_pais_nombre,
                aerolinea_pais.codigo AS aerolinea_pais_codigo,
                a.ceo_id AS aerolinea_ceo_id,
                aerolinea_ceo.nombre AS aerolinea_ceo_nombre,
                aerolinea_ceo.apellido AS aerolinea_ceo_apellido,
                a.activa AS aerolinea_activa,
                NULL AS promocion_id,
                NULL AS promocion_descripcion,
                NULL AS promocion_descuento,
                NULL AS promocion_fecha_creacion,
                NULL AS promocion_fecha_aprobacion,
                NULL AS promocion_fecha_fin,
                NULL AS promocion_estado,
                v.origen_ciudad_id,
                origen.nombre AS origen_nombre,
                origen.abreviacion AS origen_abreviacion,
                origen_pais.id AS origen_pais_id,
                origen_pais.nombre AS origen_pais_nombre,
                v.destino_ciudad_id,
                destino.nombre AS destino_nombre,
                destino.abreviacion AS destino_abreviacion,
                destino_pais.id AS destino_pais_id,
                destino_pais.nombre AS destino_pais_nombre,
                v.precio,
                v.asientos_disponibles,
                v.asientosOcupados AS asientos_ocupados,
                v.fecha_salida,
                v.fecha_llegada,
                v.fecha_creacion,
                v.distancia_km,
                v.duracion_horas,
                ev.nombre AS estado
            FROM vuelos v
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
            INNER JOIN paises aerolinea_pais ON aerolinea_pais.id = a.pais_id
            LEFT JOIN usuarios aerolinea_ceo ON aerolinea_ceo.id = a.ceo_id
            INNER JOIN ciudades origen ON origen.id = v.origen_ciudad_id
            INNER JOIN paises origen_pais ON origen_pais.id = origen.pais_id
            INNER JOIN ciudades destino ON destino.id = v.destino_ciudad_id
            INNER JOIN paises destino_pais ON destino_pais.id = destino.pais_id
            INNER JOIN estados_vuelos ev ON ev.id = v.estado_id
            WHERE v.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $vueloId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRow($row) : null;
    }

    public function hasConfirmedReservations(int $vueloId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM reservas r
            INNER JOIN estados_reservas er ON er.id = r.estado_id
            WHERE r.vuelo_id = :vuelo_id
                AND LOWER(er.nombre) = 'confirmada'
            LIMIT 1
        ");
        $stmt->execute([':vuelo_id' => $vueloId]);

        return $stmt->fetchColumn() !== false;
    }

    public function getEstadoIdByNombre(string $nombre): ?int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM estados_vuelos WHERE LOWER(nombre) = :nombre LIMIT 1');
        $stmt->execute([':nombre' => strtolower($nombre)]);
        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    public function crear(CrearVueloDto $dto, int $aerolineaId, int $estadoId): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO vuelos (
                codigoVuelo, aerolinea_id, origen_ciudad_id, destino_ciudad_id,
                precio, asientos_disponibles, fecha_salida, fecha_llegada,
                distancia_km, duracion_horas, estado_id
            ) VALUES (
                :codigo, :aerolinea, :origen, :destino, :precio, :asientos,
                :salida, :llegada, :distancia, :duracion, :estado
            )'
        );
        $stmt->execute([
            ':codigo' => $dto->codigoVuelo,
            ':aerolinea' => $aerolineaId,
            ':origen' => $dto->origenCiudadId,
            ':destino' => $dto->destinoCiudadId,
            ':precio' => $dto->precio,
            ':asientos' => $dto->asientosDisponibles,
            ':salida' => $dto->fechaSalida->format('Y-m-d H:i:s'),
            ':llegada' => $dto->fechaLlegada->format('Y-m-d H:i:s'),
            ':distancia' => $dto->distanciaKm,
            ':duracion' => $dto->duracionHoras,
            ':estado' => $estadoId,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function editar(int $vueloId, EditarVueloDto $dto): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE vuelos SET
                codigoVuelo = :codigo,
                origen_ciudad_id = :origen,
                destino_ciudad_id = :destino,
                precio = :precio,
                asientos_disponibles = :asientos,
                fecha_salida = :salida,
                fecha_llegada = :llegada,
                distancia_km = :distancia,
                duracion_horas = :duracion
            WHERE id = :id'
        );
        $stmt->execute([
            ':id' => $vueloId,
            ':codigo' => $dto->codigoVuelo,
            ':origen' => $dto->origenCiudadId,
            ':destino' => $dto->destinoCiudadId,
            ':precio' => $dto->precio,
            ':asientos' => $dto->asientosDisponibles,
            ':salida' => $dto->fechaSalida->format('Y-m-d H:i:s'),
            ':llegada' => $dto->fechaLlegada->format('Y-m-d H:i:s'),
            ':distancia' => $dto->distanciaKm,
            ':duracion' => $dto->duracionHoras,
        ]);
    }

    public function borrar(int $vueloId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM vuelos WHERE id = :id AND asientosOcupados = 0'
        );
        $stmt->execute([':id' => $vueloId]);

        if ($stmt->rowCount() !== 1) {
            throw new \RuntimeException('El vuelo no pudo ser borrado.');
        }
    }

    private function mapRow(array $row): Vuelo
    {
        $origenId = (int) $row['origen_ciudad_id'];
        $destinoId = (int) $row['destino_ciudad_id'];
        $codigoIata = strtoupper((string) $row['aerolinea_codigo_iata']);

        return new Vuelo(
            id: (int) $row['id'],
            codigoVuelo: (string) $row['codigoVuelo'],
            fechaSalida: new \DateTime($row['fecha_salida']),
            fechaLlegada: new \DateTime($row['fecha_llegada']),
            fechaCreacion: new \DateTime($row['fecha_creacion']),
            precio: (float) $row['precio'],
            distancia: (int) $row['distancia_km'],
            duracion: (float) $row['duracion_horas'],
            estado: (string) $row['estado'],
            asientosDisponibles: (int) $row['asientos_disponibles'],
            asientosOcupados: (int) $row['asientos_ocupados'],
            ciudadOrigen: [
                'idCiudad' => $origenId,
                'nombreCiudad' => (string) $row['origen_nombre'],
                'abreviacionCiudad' => (string) $row['origen_abreviacion'],
                'pais' => [
                    'idPais' => (int) $row['origen_pais_id'],
                    'nombrePais' => (string) $row['origen_pais_nombre'],
                ],
            ],
            ciudadDestino: [
                'idCiudad' => $destinoId,
                'nombreCiudad' => (string) $row['destino_nombre'],
                'abreviacionCiudad' => (string) $row['destino_abreviacion'],
                'pais' => [
                    'idPais' => (int) $row['destino_pais_id'],
                    'nombrePais' => (string) $row['destino_pais_nombre'],
                ],
            ],
            aerolinea: new Aerolinea(
                id: (int) $row['aerolinea_id'],
                nombre: (string) $row['aerolinea_nombre'],
                descripcion: (string) $row['aerolinea_descripcion'],
                codigoIata: $codigoIata,
                pais: [
                    'id' => (int) $row['aerolinea_pais_id'],
                    'nombre' => (string) $row['aerolinea_pais_nombre'],
                    'codigo' => (string) $row['aerolinea_pais_codigo'],
                ],
                ceo: $row['aerolinea_ceo_id'] === null ? null : [
                    'id' => (int) $row['aerolinea_ceo_id'],
                    'nombre' => (string) $row['aerolinea_ceo_nombre'],
                    'apellido' => (string) $row['aerolinea_ceo_apellido'],
                ],
                activa: (bool) $row['aerolinea_activa']
            ),
            promocion: $row['promocion_id'] === null ? null : [
                'idPromocion' => (int) $row['promocion_id'],
                'descripcion' => (string) $row['promocion_descripcion'],
                'descuento' => (float) $row['promocion_descuento'],
                'fechaCreacion' => (string) $row['promocion_fecha_creacion'],
                'fechaAprobacion' => $row['promocion_fecha_aprobacion'],
                'fechaFin' => $row['promocion_fecha_fin'],
                'estado' => (string) $row['promocion_estado'],
            ]
        );
    }
}
