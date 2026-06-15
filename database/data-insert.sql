USE flyto;

INSERT INTO tipos_usuarios 
    (nombre) 
VALUES
    ('administrador'),
    ('ceo'),
    ('cliente');

INSERT INTO novedades (
    titulo,
    categoria,
    texto,
    fechaPublicacion,
    fechaExpiracion
) VALUES
(
    'Mantenimiento programado',
    'Sistema',
    'El sitio permanecerá en mantenimiento el domingo de 02:00 a 05:00.',
    '2026-06-01 09:00:00',
    '2026-06-16 23:59:59'
),
(
    'Demoras por clima',
    'Vuelos',
    'Algunos vuelos pueden presentar demoras por condiciones climáticas adversas.',
    '2026-06-10 08:30:00',
    '2026-06-20 23:59:59'
),
(
    'Nuevo destino disponible',
    'Destinos',
    'Ya se encuentra disponible la búsqueda de vuelos hacia Mendoza.',
    '2026-06-12 10:00:00',
    '2026-07-12 23:59:59'
),
(
    'Actualización de políticas',
    'Reservas',
    'Se actualizaron las condiciones para cancelar reservas desde el perfil del usuario.',
    '2026-06-05 14:15:00',
    '2026-08-05 23:59:59'
),
(
    'Cierre temporal de aeropuerto',
    'Aeropuertos',
    'El aeropuerto permanecerá cerrado por obras durante la madrugada del martes.',
    '2026-05-25 12:00:00',
    '2026-06-05 23:59:59'
),
(
    'Atención al cliente extendida',
    'Soporte',
    'El horario de atención al cliente se extenderá hasta las 22:00 durante esta semana.',
    '2026-06-14 09:45:00',
    '2026-06-21 23:59:59'
),
(
    'Reprogramación de vuelos',
    'Vuelos',
    'Los pasajeros con vuelos afectados recibirán información actualizada por correo.',
    '2026-06-13 18:00:00',
    '2026-06-18 23:59:59'
),
(
    'Mejoras en el buscador',
    'Sistema',
    'Se incorporaron mejoras en los filtros de búsqueda por origen, destino y fecha.',
    '2026-06-15 11:20:00',
    '2026-09-15 23:59:59'
),
(
    'Recordatorio de documentación',
    'Usuarios',
    'Recordá verificar que tus datos personales estén actualizados antes de viajar.',
    '2026-06-01 07:00:00',
    '2026-12-31 23:59:59'
),
(
    'Novedad expirada de prueba',
    'Pruebas',
    'Esta novedad permite validar que no se muestren anuncios vencidos.',
    '2026-04-01 10:00:00',
    '2026-04-30 23:59:59'
);