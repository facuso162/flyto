<?php

namespace App\Reportes\Repositories;

use App\Reportes\Models\ReporteVentas;
use App\Reportes\Models\VueloReporteVentas;
use PDO;

require_once __DIR__ . '/../models/reporte-ventas.model.php';

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
}
