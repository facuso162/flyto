<?php

namespace App\Reportes\Repositories;

use App\Reportes\Models\ReporteVentas;
use App\Reportes\Models\ReporteVentasAdmin;
use App\Reportes\Models\ReporteOcupacion;
use App\Reportes\Models\AerolineaReporteVentas;
use App\Reportes\Models\VueloReporteVentas;
use App\Reportes\Models\VueloReporteOcupacion;
use PDO;

require_once __DIR__ . '/../models/reporte-ventas.model.php';
require_once __DIR__ . '/../models/reporte-ventas-admin.model.php';
require_once __DIR__ . '/../models/reporte-ocupacion.model.php';

class ReporteRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function generarReporteVentas(
        int $ceoId,
        \DateTimeImmutable $inicioPeriodo,
        \DateTimeImmutable $finPeriodo
    ): ReporteVentas {
        $params = [
            ':ceo_id' => $ceoId,
            ':inicio' => $inicioPeriodo->format('Y-m-d H:i:s'),
            ':fin' => $finPeriodo->format('Y-m-d H:i:s'),
        ];

        $resumenStmt = $this->pdo->prepare("
            SELECT
                COUNT(r.id) AS total_reservas,
                SUM(CASE WHEN LOWER(er.nombre) IN ('confirmada', 'completada') THEN 1 ELSE 0 END) AS reservas_completadas,
                SUM(CASE WHEN LOWER(er.nombre) IN ('confirmada', 'completada') THEN r.precio_total ELSE 0 END) AS ingresos_totales,
                SUM(CASE WHEN LOWER(er.nombre) = 'cancelada' THEN 1 ELSE 0 END) AS reservas_canceladas,
                SUM(CASE WHEN LOWER(er.nombre) = 'cancelada' THEN r.precio_total ELSE 0 END) AS monto_cancelaciones
            FROM reservas r
            INNER JOIN estados_reservas er ON er.id = r.estado_id
            INNER JOIN vuelos v ON v.id = r.vuelo_id
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
            WHERE a.ceo_id = :ceo_id
                AND r.fecha_reserva >= :inicio
                AND r.fecha_reserva < :fin
        ");
        $resumenStmt->execute($params);
        $resumen = $resumenStmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $topStmt = $this->pdo->prepare("
            SELECT
                v.codigoVuelo AS codigo_vuelo,
                origen.abreviacion AS origen,
                destino.abreviacion AS destino,
                COUNT(r.id) AS reservas,
                SUM(r.precio_total) AS ingresos
            FROM reservas r
            INNER JOIN estados_reservas er ON er.id = r.estado_id
            INNER JOIN vuelos v ON v.id = r.vuelo_id
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
            INNER JOIN ciudades origen ON origen.id = v.origen_ciudad_id
            INNER JOIN ciudades destino ON destino.id = v.destino_ciudad_id
            WHERE a.ceo_id = :ceo_id
                AND r.fecha_reserva >= :inicio
                AND r.fecha_reserva < :fin
                AND LOWER(er.nombre) IN ('confirmada', 'completada')
            GROUP BY v.id, v.codigoVuelo, origen.abreviacion, destino.abreviacion
            ORDER BY reservas DESC, ingresos DESC, v.codigoVuelo ASC
            LIMIT 5
        ");
        $topStmt->execute($params);

        $totalReservas = (int) ($resumen['total_reservas'] ?? 0);
        $canceladas = (int) ($resumen['reservas_canceladas'] ?? 0);
        $porcentajeCanceladas = $totalReservas > 0 ? round(($canceladas / $totalReservas) * 100, 1) : 0.0;

        return new ReporteVentas(
            periodo: $inicioPeriodo,
            generadoEn: new \DateTimeImmutable(),
            totalReservasCompletadas: (int) ($resumen['reservas_completadas'] ?? 0),
            totalReservasDelMes: $totalReservas,
            ingresosTotales: (float) ($resumen['ingresos_totales'] ?? 0),
            reservasCanceladas: $canceladas,
            porcentajeCanceladas: $porcentajeCanceladas,
            montoCancelaciones: (float) ($resumen['monto_cancelaciones'] ?? 0),
            topVuelos: array_map(
                fn (array $row) => new VueloReporteVentas(
                    codigoVuelo: (string) $row['codigo_vuelo'],
                    origen: (string) $row['origen'],
                    destino: (string) $row['destino'],
                    reservas: (int) $row['reservas'],
                    ingresos: (float) $row['ingresos']
                ),
                $topStmt->fetchAll(PDO::FETCH_ASSOC)
            )
        );
    }

    public function generarReporteVentasAdmin(
        \DateTimeImmutable $inicioPeriodo,
        \DateTimeImmutable $finPeriodo
    ): ReporteVentasAdmin {
        $params = [
            ':inicio' => $inicioPeriodo->format('Y-m-d H:i:s'),
            ':fin' => $finPeriodo->format('Y-m-d H:i:s'),
        ];

        $resumenStmt = $this->pdo->prepare("
            SELECT
                (SELECT COUNT(a.id) FROM aerolineas a WHERE a.ceo_id IS NOT NULL) AS aerolineas_activas,
                COUNT(r.id) AS total_reservas,
                COALESCE(SUM(r.precio_total), 0) AS ingresos_totales
            FROM reservas r
            INNER JOIN estados_reservas er ON er.id = r.estado_id
            WHERE r.fecha_reserva >= :inicio
                AND r.fecha_reserva < :fin
                AND LOWER(er.nombre) IN ('confirmada', 'completada')
        ");
        $resumenStmt->execute($params);
        $resumen = $resumenStmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $topStmt = $this->pdo->prepare("
            SELECT
                a.nombre AS aerolinea,
                COUNT(r.id) AS reservas,
                COALESCE(SUM(r.precio_total), 0) AS ingresos
            FROM reservas r
            INNER JOIN estados_reservas er ON er.id = r.estado_id
            INNER JOIN vuelos v ON v.id = r.vuelo_id
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
            WHERE r.fecha_reserva >= :inicio
                AND r.fecha_reserva < :fin
                AND LOWER(er.nombre) IN ('confirmada', 'completada')
            GROUP BY a.id, a.nombre
            ORDER BY ingresos DESC, reservas DESC, a.nombre ASC
            LIMIT 5
        ");
        $topStmt->execute($params);

        $ingresosTotales = (float) ($resumen['ingresos_totales'] ?? 0);

        return new ReporteVentasAdmin(
            periodo: $inicioPeriodo,
            generadoEn: new \DateTimeImmutable(),
            aerolineasActivas: (int) ($resumen['aerolineas_activas'] ?? 0),
            totalReservas: (int) ($resumen['total_reservas'] ?? 0),
            ingresosTotales: $ingresosTotales,
            comision: round($ingresosTotales * 0.03, 2),
            topAerolineas: array_map(
                fn (array $row) => new AerolineaReporteVentas(
                    nombre: (string) $row['aerolinea'],
                    reservas: (int) $row['reservas'],
                    ingresos: (float) $row['ingresos']
                ),
                $topStmt->fetchAll(PDO::FETCH_ASSOC)
            )
        );
    }

    public function generarReporteOcupacion(
        int $ceoId,
        \DateTimeImmutable $inicioPeriodo,
        \DateTimeImmutable $finPeriodo
    ): ReporteOcupacion {
        $params = [
            ':ceo_id' => $ceoId,
            ':inicio' => $inicioPeriodo->format('Y-m-d H:i:s'),
            ':fin' => $finPeriodo->format('Y-m-d H:i:s'),
        ];

        $resumenStmt = $this->pdo->prepare("
            SELECT
                COUNT(v.id) AS total_vuelos,
                COALESCE(AVG(CASE
                    WHEN v.asientos_disponibles > 0
                    THEN (v.asientosOcupados / v.asientos_disponibles) * 100
                    ELSE 0
                END), 0) AS ocupacion_promedio,
                SUM(CASE
                    WHEN v.asientos_disponibles > 0
                        AND (v.asientosOcupados / v.asientos_disponibles) * 100 > 90
                    THEN 1 ELSE 0
                END) AS vuelos_ocupacion_alta,
                SUM(CASE
                    WHEN v.asientos_disponibles > 0
                        AND (v.asientosOcupados / v.asientos_disponibles) * 100 < 50
                    THEN 1 ELSE 0
                END) AS vuelos_ocupacion_baja,
                COALESCE(SUM(v.asientos_disponibles), 0) AS total_asientos_disponibles,
                COALESCE(SUM(v.asientosOcupados), 0) AS total_asientos_vendidos
            FROM vuelos v
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
            WHERE a.ceo_id = :ceo_id
                AND v.fecha_salida >= :inicio
                AND v.fecha_salida < :fin
        ");
        $resumenStmt->execute($params);
        $resumen = $resumenStmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $topStmt = $this->pdo->prepare("
            SELECT
                v.codigoVuelo AS codigo_vuelo,
                origen.abreviacion AS origen,
                destino.abreviacion AS destino,
                v.asientosOcupados AS asientos_ocupados,
                v.asientos_disponibles AS asientos_disponibles,
                CASE
                    WHEN v.asientos_disponibles > 0
                    THEN (v.asientosOcupados / v.asientos_disponibles) * 100
                    ELSE 0
                END AS porcentaje_ocupacion
            FROM vuelos v
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
            INNER JOIN ciudades origen ON origen.id = v.origen_ciudad_id
            INNER JOIN ciudades destino ON destino.id = v.destino_ciudad_id
            WHERE a.ceo_id = :ceo_id
                AND v.fecha_salida >= :inicio
                AND v.fecha_salida < :fin
            ORDER BY v.asientosOcupados DESC, porcentaje_ocupacion DESC, v.codigoVuelo ASC
            LIMIT 5
        ");
        $topStmt->execute($params);

        $totalVuelos = (int) ($resumen['total_vuelos'] ?? 0);
        $ocupacionAlta = (int) ($resumen['vuelos_ocupacion_alta'] ?? 0);
        $ocupacionBaja = (int) ($resumen['vuelos_ocupacion_baja'] ?? 0);

        return new ReporteOcupacion(
            periodo: $inicioPeriodo,
            generadoEn: new \DateTimeImmutable(),
            totalVuelos: $totalVuelos,
            ocupacionPromedioGlobal: round((float) ($resumen['ocupacion_promedio'] ?? 0), 1),
            vuelosOcupacionAlta: $ocupacionAlta,
            porcentajeVuelosOcupacionAlta: $totalVuelos > 0 ? round(($ocupacionAlta / $totalVuelos) * 100, 1) : 0.0,
            vuelosOcupacionBaja: $ocupacionBaja,
            porcentajeVuelosOcupacionBaja: $totalVuelos > 0 ? round(($ocupacionBaja / $totalVuelos) * 100, 1) : 0.0,
            totalAsientosDisponibles: (int) ($resumen['total_asientos_disponibles'] ?? 0),
            totalAsientosVendidos: (int) ($resumen['total_asientos_vendidos'] ?? 0),
            topVuelos: array_map(
                fn (array $row) => new VueloReporteOcupacion(
                    codigoVuelo: (string) $row['codigo_vuelo'],
                    origen: (string) $row['origen'],
                    destino: (string) $row['destino'],
                    asientosOcupados: (int) $row['asientos_ocupados'],
                    asientosDisponibles: (int) $row['asientos_disponibles'],
                    porcentajeOcupacion: round((float) $row['porcentaje_ocupacion'], 1)
                ),
                $topStmt->fetchAll(PDO::FETCH_ASSOC)
            )
        );
    }
}
