<?php

$novedades = [
    [
        'fecha_iso' => '2026-06-08',
        'fecha' => '08 JUN. 2026',
        'categoria' => 'Experiencia de reserva',
        'titulo' => 'Nuevas herramientas para comparar opciones antes de confirmar',
        'resumen' => 'La plataforma incorpora filtros por horario, escalas y equipaje para que cada persona encuentre una alternativa clara antes de avanzar con la reserva.',
    ],
    [
        'fecha_iso' => '2026-06-03',
        'fecha' => '03 JUN. 2026',
        'categoria' => 'Operaciones',
        'titulo' => 'Alertas de cambios de itinerario disponibles en tiempo real',
        'resumen' => 'Flyto suma avisos sobre modificaciones de horario y puerta de embarque para que los pasajeros puedan anticiparse antes de llegar al aeropuerto.',
    ],
    [
        'fecha_iso' => '2026-05-22',
        'fecha' => '22 MAY. 2026',
        'categoria' => 'Actualizacion de plataforma',
        'titulo' => 'Flyto incorpora nuevas rutas internacionales para la temporada de verano 2026',
        'resumen' => 'A partir del 1 de junio, la plataforma suma conexiones directas hacia Lisboa, Tokio y Ciudad del Cabo. Los pasajeros podran reservar con hasta 11 meses de anticipacion, con tarifas de lanzamiento disponibles durante las primeras dos semanas.',
    ],
];

usort($novedades, static function (array $a, array $b): int {
    return strcmp($b['fecha_iso'], $a['fecha_iso']);
});

return $novedades;
