-- ============================================================================
-- Flyto - Carga integral de datos de prueba
-- ============================================================================
-- ATENCION: este script elimina TODOS los datos de las tablas de la aplicacion.
--
-- Base esperada: MySQL/MariaDB, con el esquema de database/creacion-tablas.sql.
-- Las fechas son relativas al momento de ejecucion para mantener vigentes los
-- vuelos pendientes, las promociones activas y parte de las novedades.
--
-- Credenciales de prueba para todas las cuentas:
--   Contrasena: Flyto2026!
--   Admin:      admin@flyto.test
--   CEOs:       ceo.austral@flyto.test
--               ceo.patagonia@flyto.test
--               ceo.andes@flyto.test
--               ceo.rioplatense@flyto.test
--   Cliente:    cliente@flyto.test
--   Pendiente:  pendiente@flyto.test (no puede iniciar sesion hasta confirmar)
--
-- Token de confirmacion de la cuenta pendiente:
--   flyto-token-confirmacion-pendiente-2026
-- ============================================================================

USE flyto;
SET NAMES utf8mb4;

SET @FLYTO_OLD_SQL_SAFE_UPDATES = @@SQL_SAFE_UPDATES;
SET SQL_SAFE_UPDATES = 0;

-- Compatibilidad con bases creadas antes de que el formulario de pasajeros
-- incorporara la nacionalidad. En el esquema actual la columna ya existe y
-- esta sentencia no realiza cambios.
ALTER TABLE pasajeros
    ADD COLUMN IF NOT EXISTS nacionalidad VARCHAR(80) NOT NULL DEFAULT 'Argentina'
    AFTER fecha_nacimiento;

START TRANSACTION;

-- --------------------------------------------------------------------------
-- Limpieza completa, respetando el orden de las claves foraneas.
-- Se usan DELETE (y no TRUNCATE) para que la carga sea transaccional.
-- --------------------------------------------------------------------------
DELETE FROM metodos_pago;
DELETE FROM pasajeros;
DELETE FROM reservas;
DELETE FROM vuelos;
DELETE FROM promociones;
DELETE FROM aerolineas;
DELETE FROM usuarios;
DELETE FROM novedades;
DELETE FROM ciudades;
DELETE FROM paises;
DELETE FROM estados_vuelos;
DELETE FROM estados_promociones;
DELETE FROM estados_reservas;
DELETE FROM tipos_usuarios;

-- --------------------------------------------------------------------------
-- Catalogos
-- --------------------------------------------------------------------------
INSERT INTO tipos_usuarios (nombre) VALUES
    ('administrador'),
    ('ceo'),
    ('cliente');

INSERT INTO estados_reservas (nombre) VALUES
    ('confirmada'),
    ('cancelada'),
    ('completada');

INSERT INTO estados_vuelos (nombre) VALUES
    ('pendiente'),
    ('completado'),
    ('cancelado');

INSERT INTO estados_promociones (nombre) VALUES
    ('activa'),
    ('inactiva'),
    ('pendiente_activacion');

-- --------------------------------------------------------------------------
-- Paises y ciudades
-- --------------------------------------------------------------------------
INSERT INTO paises (nombre, codigo) VALUES
    ('Argentina', 'ARG'),
    ('Brasil', 'BRA'),
    ('Chile', 'CHL'),
    ('Uruguay', 'URY'),
    ('Paraguay', 'PRY'),
    ('Perú', 'PER'),
    ('Colombia', 'COL'),
    ('México', 'MEX');

INSERT INTO ciudades (nombre, abreviacion, pais_id) VALUES
    ('Buenos Aires',       'BUE', (SELECT id FROM paises WHERE codigo = 'ARG')),
    ('Córdoba',            'COR', (SELECT id FROM paises WHERE codigo = 'ARG')),
    ('Rosario',            'ROS', (SELECT id FROM paises WHERE codigo = 'ARG')),
    ('Mendoza',            'MDZ', (SELECT id FROM paises WHERE codigo = 'ARG')),
    ('Salta',              'SLA', (SELECT id FROM paises WHERE codigo = 'ARG')),
    ('San Carlos de Bariloche', 'BRC', (SELECT id FROM paises WHERE codigo = 'ARG')),
    ('Puerto Iguazú',      'IGR', (SELECT id FROM paises WHERE codigo = 'ARG')),
    ('Ushuaia',            'USH', (SELECT id FROM paises WHERE codigo = 'ARG')),
    ('São Paulo',          'SAO', (SELECT id FROM paises WHERE codigo = 'BRA')),
    ('Río de Janeiro',     'RIO', (SELECT id FROM paises WHERE codigo = 'BRA')),
    ('Santiago de Chile',  'SCL', (SELECT id FROM paises WHERE codigo = 'CHL')),
    ('Montevideo',         'MVD', (SELECT id FROM paises WHERE codigo = 'URY')),
    ('Asunción',           'ASU', (SELECT id FROM paises WHERE codigo = 'PRY')),
    ('Lima',               'LIM', (SELECT id FROM paises WHERE codigo = 'PER')),
    ('Cusco',              'CUZ', (SELECT id FROM paises WHERE codigo = 'PER')),
    ('Bogotá',             'BOG', (SELECT id FROM paises WHERE codigo = 'COL')),
    ('Ciudad de México',   'MEX', (SELECT id FROM paises WHERE codigo = 'MEX')),
    ('Cancún',             'CUN', (SELECT id FROM paises WHERE codigo = 'MEX'));

-- --------------------------------------------------------------------------
-- Novedades: vigentes, proximas a vencer y expiradas
-- --------------------------------------------------------------------------
INSERT INTO novedades (
    titulo,
    categoria,
    texto,
    fechaPublicacion,
    fechaExpiracion
) VALUES
    (
        'Nuevos destinos regionales',
        'Destinos',
        'Flyto incorpora nuevas rutas hacia Bogotá, Cusco y Cancún.',
        DATE_SUB(NOW(), INTERVAL 5 DAY),
        DATE_ADD(NOW(), INTERVAL 90 DAY)
    ),
    (
        'Promociones de temporada',
        'Promociones',
        'Encontrá descuentos vigentes para vuelos nacionales e internacionales.',
        DATE_SUB(NOW(), INTERVAL 3 DAY),
        DATE_ADD(NOW(), INTERVAL 45 DAY)
    ),
    (
        'Recordatorio de documentación',
        'Viajes',
        'Verificá la vigencia de tu documento y pasaporte antes de viajar.',
        DATE_SUB(NOW(), INTERVAL 10 DAY),
        DATE_ADD(NOW(), INTERVAL 60 DAY)
    ),
    (
        'Mejoras en el buscador',
        'Sistema',
        'Ahora podés comparar más rutas, fechas y disponibilidad de asientos.',
        DATE_SUB(NOW(), INTERVAL 2 DAY),
        DATE_ADD(NOW(), INTERVAL 120 DAY)
    ),
    (
        'Atención al cliente extendida',
        'Soporte',
        'El equipo de soporte amplía su horario durante la temporada alta.',
        DATE_SUB(NOW(), INTERVAL 7 DAY),
        DATE_ADD(NOW(), INTERVAL 20 DAY)
    ),
    (
        'Operación normal en aeropuertos',
        'Vuelos',
        'Los principales aeropuertos de la región operan con normalidad.',
        DATE_SUB(NOW(), INTERVAL 1 DAY),
        DATE_ADD(NOW(), INTERVAL 7 DAY)
    ),
    (
        'Consejos para viajar en familia',
        'Guías',
        'Revisá los requisitos para menores y organizá la documentación con tiempo.',
        DATE_SUB(NOW(), INTERVAL 15 DAY),
        DATE_ADD(NOW(), INTERVAL 75 DAY)
    ),
    (
        'Actualización de políticas de cancelación',
        'Reservas',
        'Consultá las condiciones actualizadas antes de cancelar una reserva.',
        DATE_SUB(NOW(), INTERVAL 4 DAY),
        DATE_ADD(NOW(), INTERVAL 30 DAY)
    ),
    (
        'Campaña de otoño finalizada',
        'Promociones',
        'La campaña promocional de otoño ha finalizado.',
        DATE_SUB(NOW(), INTERVAL 100 DAY),
        DATE_SUB(NOW(), INTERVAL 40 DAY)
    ),
    (
        'Mantenimiento completado',
        'Sistema',
        'Las tareas de mantenimiento programado finalizaron correctamente.',
        DATE_SUB(NOW(), INTERVAL 60 DAY),
        DATE_SUB(NOW(), INTERVAL 30 DAY)
    ),
    (
        'Aviso meteorológico anterior',
        'Vuelos',
        'El aviso meteorológico ya no se encuentra vigente.',
        DATE_SUB(NOW(), INTERVAL 35 DAY),
        DATE_SUB(NOW(), INTERVAL 20 DAY)
    ),
    (
        'Beneficio de verano finalizado',
        'Promociones',
        'El beneficio especial de verano dejó de estar disponible.',
        DATE_SUB(NOW(), INTERVAL 180 DAY),
        DATE_SUB(NOW(), INTERVAL 120 DAY)
    );

-- --------------------------------------------------------------------------
-- Usuarios: exactamente 1 administrador, 4 CEOs y 2 clientes.
-- El hash bcrypt corresponde a la contrasena Flyto2026!
-- --------------------------------------------------------------------------
INSERT INTO usuarios (
    nombre,
    apellido,
    email,
    telefono,
    clave_hash,
    tipo_usuario_id,
    activo,
    fecha_registro,
    email_verificado,
    token_verificacion,
    token_recupero,
    token_expiracion
) VALUES
    (
        'Ada',
        'Administrador',
        'admin@flyto.test',
        '1120000001',
        '$2y$10$YlqmwSMAmXuEYIH3Ao4zr.wmlnhinHOtts3QxvBTgUC5b3/hyhp8C',
        (SELECT id FROM tipos_usuarios WHERE nombre = 'administrador'),
        TRUE,
        DATE_SUB(NOW(), INTERVAL 300 DAY),
        TRUE,
        NULL,
        NULL,
        NULL
    ),
    (
        'Lucía',
        'Fernández',
        'ceo.austral@flyto.test',
        '1120000101',
        '$2y$10$YlqmwSMAmXuEYIH3Ao4zr.wmlnhinHOtts3QxvBTgUC5b3/hyhp8C',
        (SELECT id FROM tipos_usuarios WHERE nombre = 'ceo'),
        TRUE,
        DATE_SUB(NOW(), INTERVAL 240 DAY),
        TRUE,
        NULL,
        NULL,
        NULL
    ),
    (
        'Mateo',
        'Ríos',
        'ceo.patagonia@flyto.test',
        '1120000102',
        '$2y$10$YlqmwSMAmXuEYIH3Ao4zr.wmlnhinHOtts3QxvBTgUC5b3/hyhp8C',
        (SELECT id FROM tipos_usuarios WHERE nombre = 'ceo'),
        TRUE,
        DATE_SUB(NOW(), INTERVAL 180 DAY),
        TRUE,
        NULL,
        NULL,
        NULL
    ),
    (
        'Valentina',
        'Silva',
        'ceo.andes@flyto.test',
        '1120000103',
        '$2y$10$YlqmwSMAmXuEYIH3Ao4zr.wmlnhinHOtts3QxvBTgUC5b3/hyhp8C',
        (SELECT id FROM tipos_usuarios WHERE nombre = 'ceo'),
        TRUE,
        DATE_SUB(NOW(), INTERVAL 90 DAY),
        TRUE,
        NULL,
        NULL,
        NULL
    ),
    (
        'Joaquín',
        'Pereira',
        'ceo.rioplatense@flyto.test',
        '1120000104',
        '$2y$10$YlqmwSMAmXuEYIH3Ao4zr.wmlnhinHOtts3QxvBTgUC5b3/hyhp8C',
        (SELECT id FROM tipos_usuarios WHERE nombre = 'ceo'),
        TRUE,
        DATE_SUB(NOW(), INTERVAL 20 DAY),
        TRUE,
        NULL,
        NULL,
        NULL
    ),
    (
        'Sofía',
        'Gómez',
        'cliente@flyto.test',
        '1120000201',
        '$2y$10$YlqmwSMAmXuEYIH3Ao4zr.wmlnhinHOtts3QxvBTgUC5b3/hyhp8C',
        (SELECT id FROM tipos_usuarios WHERE nombre = 'cliente'),
        TRUE,
        DATE_SUB(NOW(), INTERVAL 150 DAY),
        TRUE,
        NULL,
        NULL,
        NULL
    ),
    (
        'Tomás',
        'Martínez',
        'pendiente@flyto.test',
        '1120000202',
        '$2y$10$YlqmwSMAmXuEYIH3Ao4zr.wmlnhinHOtts3QxvBTgUC5b3/hyhp8C',
        (SELECT id FROM tipos_usuarios WHERE nombre = 'cliente'),
        TRUE,
        DATE_SUB(NOW(), INTERVAL 2 DAY),
        FALSE,
        'flyto-token-confirmacion-pendiente-2026',
        NULL,
        NULL
    );

-- --------------------------------------------------------------------------
-- Aerolineas: una para cada CEO
-- --------------------------------------------------------------------------
INSERT INTO aerolineas (
    nombre,
    descripcion,
    codigo_iata,
    pais_id,
    ceo_id,
    activa
) VALUES
    (
        'Austral Air',
        'Aerolínea argentina con amplia cobertura nacional y regional.',
        'AUA',
        (SELECT id FROM paises WHERE codigo = 'ARG'),
        (SELECT id FROM usuarios WHERE email = 'ceo.austral@flyto.test'),
        TRUE
    ),
    (
        'Patagonia Jet',
        'Especialista en rutas turísticas del sur argentino y conexiones regionales.',
        'PTJ',
        (SELECT id FROM paises WHERE codigo = 'ARG'),
        (SELECT id FROM usuarios WHERE email = 'ceo.patagonia@flyto.test'),
        TRUE
    ),
    (
        'Andes Pacific',
        'Aerolínea chilena que conecta destinos de la costa del Pacífico.',
        'ANP',
        (SELECT id FROM paises WHERE codigo = 'CHL'),
        (SELECT id FROM usuarios WHERE email = 'ceo.andes@flyto.test'),
        TRUE
    ),
    (
        'Río de la Plata Air',
        'Compañía uruguaya orientada a rutas del Mercosur.',
        'RPA',
        (SELECT id FROM paises WHERE codigo = 'URY'),
        (SELECT id FROM usuarios WHERE email = 'ceo.rioplatense@flyto.test'),
        TRUE
    );

-- --------------------------------------------------------------------------
-- Promociones: 4 activas, 4 inactivas y 4 pendientes de activacion.
-- Hay una promocion activa por aerolinea para evitar resultados duplicados.
-- --------------------------------------------------------------------------
INSERT INTO promociones (
    descripcion,
    descuento,
    fecha_creacion,
    fecha_aprobacion,
    fecha_fin,
    estado_id,
    aerolinea_id,
    activa
) VALUES
    (
        '15% de descuento en rutas nacionales de Austral Air.',
        0.150,
        DATE_SUB(NOW(), INTERVAL 50 DAY),
        DATE_SUB(NOW(), INTERVAL 45 DAY),
        DATE_ADD(NOW(), INTERVAL 120 DAY),
        (SELECT id FROM estados_promociones WHERE nombre = 'activa'),
        (SELECT id FROM aerolineas WHERE codigo_iata = 'AUA'),
        TRUE
    ),
    (
        '10% para escapadas a la Patagonia.',
        0.100,
        DATE_SUB(NOW(), INTERVAL 40 DAY),
        DATE_SUB(NOW(), INTERVAL 35 DAY),
        DATE_ADD(NOW(), INTERVAL 100 DAY),
        (SELECT id FROM estados_promociones WHERE nombre = 'activa'),
        (SELECT id FROM aerolineas WHERE codigo_iata = 'PTJ'),
        TRUE
    ),
    (
        '12% en conexiones por la costa del Pacífico.',
        0.120,
        DATE_SUB(NOW(), INTERVAL 30 DAY),
        DATE_SUB(NOW(), INTERVAL 25 DAY),
        DATE_ADD(NOW(), INTERVAL 90 DAY),
        (SELECT id FROM estados_promociones WHERE nombre = 'activa'),
        (SELECT id FROM aerolineas WHERE codigo_iata = 'ANP'),
        TRUE
    ),
    (
        '8% en rutas seleccionadas del Mercosur.',
        0.080,
        DATE_SUB(NOW(), INTERVAL 20 DAY),
        DATE_SUB(NOW(), INTERVAL 15 DAY),
        DATE_ADD(NOW(), INTERVAL 80 DAY),
        (SELECT id FROM estados_promociones WHERE nombre = 'activa'),
        (SELECT id FROM aerolineas WHERE codigo_iata = 'RPA'),
        TRUE
    ),
    (
        'Beneficio anterior para vuelos a Mendoza.',
        0.200,
        DATE_SUB(NOW(), INTERVAL 160 DAY),
        NULL,
        NULL,
        (SELECT id FROM estados_promociones WHERE nombre = 'inactiva'),
        (SELECT id FROM aerolineas WHERE codigo_iata = 'AUA'),
        TRUE
    ),
    (
        'Campaña de invierno archivada.',
        0.180,
        DATE_SUB(NOW(), INTERVAL 140 DAY),
        NULL,
        NULL,
        (SELECT id FROM estados_promociones WHERE nombre = 'inactiva'),
        (SELECT id FROM aerolineas WHERE codigo_iata = 'PTJ'),
        TRUE
    ),
    (
        'Descuento anterior para vuelos a Lima.',
        0.160,
        DATE_SUB(NOW(), INTERVAL 120 DAY),
        NULL,
        NULL,
        (SELECT id FROM estados_promociones WHERE nombre = 'inactiva'),
        (SELECT id FROM aerolineas WHERE codigo_iata = 'ANP'),
        TRUE
    ),
    (
        'Promoción regional fuera de vigencia.',
        0.140,
        DATE_SUB(NOW(), INTERVAL 100 DAY),
        NULL,
        NULL,
        (SELECT id FROM estados_promociones WHERE nombre = 'inactiva'),
        (SELECT id FROM aerolineas WHERE codigo_iata = 'RPA'),
        TRUE
    ),
    (
        'Solicitud: 22% para el corredor Buenos Aires - Santiago.',
        0.220,
        DATE_SUB(NOW(), INTERVAL 8 DAY),
        NULL,
        DATE_ADD(NOW(), INTERVAL 70 DAY),
        (SELECT id FROM estados_promociones WHERE nombre = 'pendiente_activacion'),
        (SELECT id FROM aerolineas WHERE codigo_iata = 'AUA'),
        TRUE
    ),
    (
        'Solicitud: 20% en vuelos a Ushuaia.',
        0.200,
        DATE_SUB(NOW(), INTERVAL 6 DAY),
        NULL,
        DATE_ADD(NOW(), INTERVAL 65 DAY),
        (SELECT id FROM estados_promociones WHERE nombre = 'pendiente_activacion'),
        (SELECT id FROM aerolineas WHERE codigo_iata = 'PTJ'),
        TRUE
    ),
    (
        'Solicitud: 18% para conocer Bogotá.',
        0.180,
        DATE_SUB(NOW(), INTERVAL 4 DAY),
        NULL,
        DATE_ADD(NOW(), INTERVAL 60 DAY),
        (SELECT id FROM estados_promociones WHERE nombre = 'pendiente_activacion'),
        (SELECT id FROM aerolineas WHERE codigo_iata = 'ANP'),
        TRUE
    ),
    (
        'Solicitud: 16% en conexiones Asunción - Río.',
        0.160,
        DATE_SUB(NOW(), INTERVAL 2 DAY),
        NULL,
        DATE_ADD(NOW(), INTERVAL 55 DAY),
        (SELECT id FROM estados_promociones WHERE nombre = 'pendiente_activacion'),
        (SELECT id FROM aerolineas WHERE codigo_iata = 'RPA'),
        TRUE
    );

-- --------------------------------------------------------------------------
-- Vuelos
-- 12 pendientes, 8 completados y 4 cancelados.
-- asientos_disponibles representa la capacidad total; asientosOcupados, los
-- asientos vendidos, tal como los utiliza la aplicacion.
-- --------------------------------------------------------------------------
INSERT INTO vuelos (
    codigoVuelo,
    aerolinea_id,
    origen_ciudad_id,
    destino_ciudad_id,
    precio,
    asientos_disponibles,
    asientosOcupados,
    fecha_salida,
    fecha_llegada,
    fecha_creacion,
    distancia_km,
    duracion_horas,
    estado_id
) VALUES
    -- Pendientes: Austral Air
    (
        'AA1001',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'AUA'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        (SELECT id FROM ciudades WHERE abreviacion = 'MDZ'),
        145000.00, 180, 96,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 3 DAY), INTERVAL 8 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 3 DAY), INTERVAL 10 HOUR),
        DATE_SUB(NOW(), INTERVAL 90 DAY),
        985, 2.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
    ),
    (
        'AA1002',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'AUA'),
        (SELECT id FROM ciudades WHERE abreviacion = 'MDZ'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        139000.00, 180, 168,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 4 DAY), INTERVAL 18 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 4 DAY), INTERVAL 20 HOUR),
        DATE_SUB(NOW(), INTERVAL 85 DAY),
        985, 2.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
    ),
    (
        'AA1003',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'AUA'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        (SELECT id FROM ciudades WHERE abreviacion = 'SCL'),
        245000.00, 160, 34,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 7 DAY), INTERVAL 7 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 7 DAY), INTERVAL 10 HOUR),
        DATE_SUB(NOW(), INTERVAL 75 DAY),
        1140, 3.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
    ),
    -- Pendientes: Patagonia Jet
    (
        'PJ2001',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'PTJ'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BRC'),
        165000.00, 150, 72,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 2 DAY), INTERVAL 6 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 2 DAY), INTERVAL 8 HOUR),
        DATE_SUB(NOW(), INTERVAL 70 DAY),
        1345, 2.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
    ),
    (
        'PJ2002',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'PTJ'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BRC'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        159000.00, 150, 145,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 3 DAY), INTERVAL 17 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 3 DAY), INTERVAL 19 HOUR),
        DATE_SUB(NOW(), INTERVAL 68 DAY),
        1345, 2.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
    ),
    (
        'PJ2003',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'PTJ'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        (SELECT id FROM ciudades WHERE abreviacion = 'USH'),
        225000.00, 140, 18,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 8 DAY), INTERVAL 5 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 8 DAY), INTERVAL 9 HOUR),
        DATE_SUB(NOW(), INTERVAL 60 DAY),
        2370, 4.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
    ),
    -- Pendientes: Andes Pacific
    (
        'AP3001',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'ANP'),
        (SELECT id FROM ciudades WHERE abreviacion = 'SCL'),
        (SELECT id FROM ciudades WHERE abreviacion = 'LIM'),
        285000.00, 190, 105,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 5 DAY), INTERVAL 9 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 5 DAY), INTERVAL 13 HOUR),
        DATE_SUB(NOW(), INTERVAL 65 DAY),
        2460, 4.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
    ),
    (
        'AP3002',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'ANP'),
        (SELECT id FROM ciudades WHERE abreviacion = 'LIM'),
        (SELECT id FROM ciudades WHERE abreviacion = 'SCL'),
        279000.00, 190, 176,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 6 DAY), INTERVAL 14 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 6 DAY), INTERVAL 18 HOUR),
        DATE_SUB(NOW(), INTERVAL 63 DAY),
        2460, 4.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
    ),
    (
        'AP3003',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'ANP'),
        (SELECT id FROM ciudades WHERE abreviacion = 'SCL'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BOG'),
        345000.00, 170, 21,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 9 DAY), INTERVAL 10 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 9 DAY), INTERVAL 16 HOUR),
        DATE_SUB(NOW(), INTERVAL 55 DAY),
        4250, 6.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
    ),
    -- Pendientes: Rio de la Plata Air
    (
        'RP4001',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'RPA'),
        (SELECT id FROM ciudades WHERE abreviacion = 'MVD'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        98000.00, 120, 61,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 DAY), INTERVAL 11 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 DAY), INTERVAL 12 HOUR),
        DATE_SUB(NOW(), INTERVAL 50 DAY),
        220, 1.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
    ),
    (
        'RP4002',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'RPA'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        (SELECT id FROM ciudades WHERE abreviacion = 'MVD'),
        96000.00, 120, 116,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 2 DAY), INTERVAL 16 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 2 DAY), INTERVAL 17 HOUR),
        DATE_SUB(NOW(), INTERVAL 48 DAY),
        220, 1.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
    ),
    (
        'RP4003',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'RPA'),
        (SELECT id FROM ciudades WHERE abreviacion = 'ASU'),
        (SELECT id FROM ciudades WHERE abreviacion = 'RIO'),
        238000.00, 145, 13,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 10 DAY), INTERVAL 7 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 10 DAY), INTERVAL 10 HOUR),
        DATE_SUB(NOW(), INTERVAL 45 DAY),
        1830, 3.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'pendiente')
    ),

    -- Completados
    (
        'AA1101',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'AUA'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        (SELECT id FROM ciudades WHERE abreviacion = 'COR'),
        118000.00, 180, 174,
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 8 HOUR), INTERVAL 60 DAY),
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 9 HOUR), INTERVAL 60 DAY),
        DATE_SUB(NOW(), INTERVAL 150 DAY),
        695, 1.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'completado')
    ),
    (
        'AA1102',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'AUA'),
        (SELECT id FROM ciudades WHERE abreviacion = 'COR'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        116000.00, 180, 126,
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 19 HOUR), INTERVAL 50 DAY),
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 20 HOUR), INTERVAL 50 DAY),
        DATE_SUB(NOW(), INTERVAL 145 DAY),
        695, 1.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'completado')
    ),
    (
        'PJ2101',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'PTJ'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        (SELECT id FROM ciudades WHERE abreviacion = 'IGR'),
        142000.00, 150, 149,
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 7 HOUR), INTERVAL 45 DAY),
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 9 HOUR), INTERVAL 45 DAY),
        DATE_SUB(NOW(), INTERVAL 130 DAY),
        1050, 2.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'completado')
    ),
    (
        'PJ2102',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'PTJ'),
        (SELECT id FROM ciudades WHERE abreviacion = 'IGR'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        138000.00, 150, 88,
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 18 HOUR), INTERVAL 35 DAY),
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 20 HOUR), INTERVAL 35 DAY),
        DATE_SUB(NOW(), INTERVAL 125 DAY),
        1050, 2.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'completado')
    ),
    (
        'AP3101',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'ANP'),
        (SELECT id FROM ciudades WHERE abreviacion = 'SCL'),
        (SELECT id FROM ciudades WHERE abreviacion = 'CUZ'),
        310000.00, 190, 188,
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 6 HOUR), INTERVAL 70 DAY),
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 10 HOUR), INTERVAL 70 DAY),
        DATE_SUB(NOW(), INTERVAL 160 DAY),
        2220, 4.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'completado')
    ),
    (
        'AP3102',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'ANP'),
        (SELECT id FROM ciudades WHERE abreviacion = 'LIM'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BOG'),
        295000.00, 190, 102,
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 12 HOUR), INTERVAL 25 DAY),
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 15 HOUR), INTERVAL 25 DAY),
        DATE_SUB(NOW(), INTERVAL 115 DAY),
        1880, 3.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'completado')
    ),
    (
        'RP4101',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'RPA'),
        (SELECT id FROM ciudades WHERE abreviacion = 'MVD'),
        (SELECT id FROM ciudades WHERE abreviacion = 'SAO'),
        225000.00, 145, 141,
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 9 HOUR), INTERVAL 20 DAY),
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 12 HOUR), INTERVAL 20 DAY),
        DATE_SUB(NOW(), INTERVAL 100 DAY),
        1560, 3.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'completado')
    ),
    (
        'RP4102',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'RPA'),
        (SELECT id FROM ciudades WHERE abreviacion = 'SAO'),
        (SELECT id FROM ciudades WHERE abreviacion = 'MVD'),
        219000.00, 145, 73,
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 15 HOUR), INTERVAL 10 DAY),
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 18 HOUR), INTERVAL 10 DAY),
        DATE_SUB(NOW(), INTERVAL 95 DAY),
        1560, 3.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'completado')
    ),

    -- Cancelados
    (
        'AA1201',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'AUA'),
        (SELECT id FROM ciudades WHERE abreviacion = 'ROS'),
        (SELECT id FROM ciudades WHERE abreviacion = 'SLA'),
        132000.00, 130, 0,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 11 DAY), INTERVAL 10 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 11 DAY), INTERVAL 12 HOUR),
        DATE_SUB(NOW(), INTERVAL 35 DAY),
        930, 2.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'cancelado')
    ),
    (
        'PJ2201',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'PTJ'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        (SELECT id FROM ciudades WHERE abreviacion = 'CUN'),
        520000.00, 170, 0,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 15 DAY), INTERVAL 23 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 16 DAY), INTERVAL 8 HOUR),
        DATE_SUB(NOW(), INTERVAL 40 DAY),
        6900, 9.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'cancelado')
    ),
    (
        'AP3201',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'ANP'),
        (SELECT id FROM ciudades WHERE abreviacion = 'SCL'),
        (SELECT id FROM ciudades WHERE abreviacion = 'MEX'),
        475000.00, 180, 0,
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 18 DAY), INTERVAL 22 HOUR),
        DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 19 DAY), INTERVAL 6 HOUR),
        DATE_SUB(NOW(), INTERVAL 45 DAY),
        6600, 8.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'cancelado')
    ),
    (
        'RP4201',
        (SELECT id FROM aerolineas WHERE codigo_iata = 'RPA'),
        (SELECT id FROM ciudades WHERE abreviacion = 'ASU'),
        (SELECT id FROM ciudades WHERE abreviacion = 'BUE'),
        185000.00, 145, 0,
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 13 HOUR), INTERVAL 5 DAY),
        DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 15 HOUR), INTERVAL 5 DAY),
        DATE_SUB(NOW(), INTERVAL 60 DAY),
        1040, 2.00,
        (SELECT id FROM estados_vuelos WHERE nombre = 'cancelado')
    );

-- --------------------------------------------------------------------------
-- Reservas: 8 confirmadas, 8 canceladas y 8 completadas.
-- Cada fila semilla indica vuelo, cliente, cantidad de pasajeros, antiguedad de
-- la reserva y factor historico aplicado al precio.
-- --------------------------------------------------------------------------
INSERT INTO reservas (
    usuario_id,
    vuelo_id,
    precio_total,
    fecha_reserva,
    estado_id
)
SELECT
    u.id,
    v.id,
    ROUND(v.precio * semilla.cantidad * semilla.factor_precio, 2),
    DATE_ADD(NOW(), INTERVAL semilla.dias_reserva DAY),
    er.id
FROM (
    SELECT 'AA1001' AS codigo_vuelo, 'cliente@flyto.test' AS email, 2 AS cantidad, -12 AS dias_reserva, 0.850 AS factor_precio, 'confirmada' AS estado
    UNION ALL SELECT 'AA1002', 'pendiente@flyto.test', 1, -10, 0.850, 'confirmada'
    UNION ALL SELECT 'AA1003', 'cliente@flyto.test', 3, -9, 1.000, 'cancelada'
    UNION ALL SELECT 'PJ2001', 'cliente@flyto.test', 2, -11, 0.900, 'confirmada'
    UNION ALL SELECT 'PJ2002', 'pendiente@flyto.test', 1, -8, 0.900, 'confirmada'
    UNION ALL SELECT 'PJ2003', 'cliente@flyto.test', 2, -7, 1.000, 'cancelada'
    UNION ALL SELECT 'AP3001', 'cliente@flyto.test', 2, -13, 0.880, 'confirmada'
    UNION ALL SELECT 'AP3002', 'pendiente@flyto.test', 1, -6, 0.880, 'confirmada'
    UNION ALL SELECT 'AP3003', 'cliente@flyto.test', 1, -5, 1.000, 'cancelada'
    UNION ALL SELECT 'RP4001', 'cliente@flyto.test', 2, -14, 0.920, 'confirmada'
    UNION ALL SELECT 'RP4002', 'pendiente@flyto.test', 1, -4, 0.920, 'confirmada'
    UNION ALL SELECT 'RP4003', 'cliente@flyto.test', 3, -3, 1.000, 'cancelada'

    UNION ALL SELECT 'AA1101', 'cliente@flyto.test', 2, -90, 0.900, 'completada'
    UNION ALL SELECT 'AA1102', 'pendiente@flyto.test', 1, -80, 1.000, 'completada'
    UNION ALL SELECT 'PJ2101', 'cliente@flyto.test', 3, -75, 0.850, 'completada'
    UNION ALL SELECT 'PJ2102', 'pendiente@flyto.test', 1, -65, 1.000, 'completada'
    UNION ALL SELECT 'AP3101', 'cliente@flyto.test', 2, -100, 0.800, 'completada'
    UNION ALL SELECT 'AP3102', 'pendiente@flyto.test', 1, -55, 0.900, 'completada'
    UNION ALL SELECT 'RP4101', 'cliente@flyto.test', 2, -50, 0.900, 'completada'
    UNION ALL SELECT 'RP4102', 'pendiente@flyto.test', 1, -40, 1.000, 'completada'

    UNION ALL SELECT 'AA1201', 'cliente@flyto.test', 1, -15, 1.000, 'cancelada'
    UNION ALL SELECT 'PJ2201', 'pendiente@flyto.test', 2, -18, 0.950, 'cancelada'
    UNION ALL SELECT 'AP3201', 'cliente@flyto.test', 1, -20, 1.000, 'cancelada'
    UNION ALL SELECT 'RP4201', 'pendiente@flyto.test', 2, -25, 1.000, 'cancelada'
) AS semilla
INNER JOIN usuarios u
    ON u.email = semilla.email
INNER JOIN vuelos v
    ON v.codigoVuelo = semilla.codigo_vuelo
INNER JOIN estados_reservas er
    ON er.nombre = semilla.estado;

-- --------------------------------------------------------------------------
-- Pasajeros
-- Todas las reservas tienen un pasajero titular; algunas tienen dos o tres.
-- --------------------------------------------------------------------------
INSERT INTO pasajeros (
    reserva_id,
    nombre,
    apellido,
    documento,
    pasaporte,
    fecha_nacimiento,
    nacionalidad,
    telefono_contacto,
    correo_electronico
)
SELECT
    r.id,
    u.nombre,
    u.apellido,
    CONCAT('DNI-', LPAD(r.id, 8, '0')),
    CONCAT('ARG-', LPAD(r.id, 7, '0')),
    CASE
        WHEN u.email = 'cliente@flyto.test' THEN '1992-05-14'
        ELSE '1988-11-03'
    END,
    'Argentina',
    u.telefono,
    u.email
FROM reservas r
INNER JOIN usuarios u ON u.id = r.usuario_id;

-- Segundo pasajero para las reservas grupales.
INSERT INTO pasajeros (
    reserva_id,
    nombre,
    apellido,
    documento,
    pasaporte,
    fecha_nacimiento,
    nacionalidad,
    telefono_contacto,
    correo_electronico
)
SELECT
    r.id,
    'Camila',
    'López',
    CONCAT('DNI-C-', LPAD(r.id, 6, '0')),
    CONCAT('ARG-C-', LPAD(r.id, 5, '0')),
    '1994-08-22',
    'Argentina',
    '1120000301',
    'camila.acompanante@flyto.test'
FROM reservas r
INNER JOIN vuelos v ON v.id = r.vuelo_id
WHERE v.codigoVuelo IN (
    'AA1001', 'AA1003', 'PJ2001', 'PJ2003', 'AP3001', 'RP4001', 'RP4003',
    'AA1101', 'PJ2101', 'AP3101', 'RP4101', 'PJ2201', 'RP4201'
);

-- Tercer pasajero para reservas de tres personas.
INSERT INTO pasajeros (
    reserva_id,
    nombre,
    apellido,
    documento,
    pasaporte,
    fecha_nacimiento,
    nacionalidad,
    telefono_contacto,
    correo_electronico
)
SELECT
    r.id,
    'Bruno',
    'Gómez',
    CONCAT('DNI-B-', LPAD(r.id, 6, '0')),
    CONCAT('ARG-B-', LPAD(r.id, 5, '0')),
    '2012-02-17',
    'Argentina',
    '1120000302',
    'bruno.acompanante@flyto.test'
FROM reservas r
INNER JOIN vuelos v ON v.id = r.vuelo_id
WHERE v.codigoVuelo IN ('AA1003', 'RP4003', 'PJ2101');

-- --------------------------------------------------------------------------
-- Metodos de pago: un pago utilizado por cada reserva.
-- Los cuatro ultimos digitos y vencimientos varian para facilitar las pruebas.
-- --------------------------------------------------------------------------
INSERT INTO metodos_pago (
    reserva_id,
    nombre_titular,
    ultimos_cuatro_digitos,
    vencimiento_mes,
    vencimiento_anio,
    fecha_pago
)
SELECT
    r.id,
    CONCAT(u.nombre, ' ', u.apellido),
    LPAD(MOD(r.id * 137, 10000), 4, '0'),
    1 + MOD(r.id, 12),
    YEAR(CURDATE()) + 2 + MOD(r.id, 4),
    DATE_ADD(r.fecha_reserva, INTERVAL 5 MINUTE)
FROM reservas r
INNER JOIN usuarios u ON u.id = r.usuario_id;

COMMIT;

SET SQL_SAFE_UPDATES = @FLYTO_OLD_SQL_SAFE_UPDATES;

-- --------------------------------------------------------------------------
-- Resumen de control. Estos SELECT permiten verificar la carga al ejecutarla.
-- --------------------------------------------------------------------------
SELECT tu.nombre AS tipo_usuario, COUNT(*) AS cantidad
FROM usuarios u
INNER JOIN tipos_usuarios tu ON tu.id = u.tipo_usuario_id
GROUP BY tu.id, tu.nombre
ORDER BY tu.id;

SELECT ev.nombre AS estado_vuelo, COUNT(*) AS cantidad
FROM vuelos v
INNER JOIN estados_vuelos ev ON ev.id = v.estado_id
GROUP BY ev.id, ev.nombre
ORDER BY ev.id;

SELECT ep.nombre AS estado_promocion, COUNT(*) AS cantidad
FROM promociones p
INNER JOIN estados_promociones ep ON ep.id = p.estado_id
GROUP BY ep.id, ep.nombre
ORDER BY ep.id;

SELECT er.nombre AS estado_reserva, COUNT(*) AS cantidad
FROM reservas r
INNER JOIN estados_reservas er ON er.id = r.estado_id
GROUP BY er.id, er.nombre
ORDER BY er.id;

SELECT
    (SELECT COUNT(*) FROM paises) AS paises,
    (SELECT COUNT(*) FROM ciudades) AS ciudades,
    (SELECT COUNT(*) FROM novedades) AS novedades,
    (SELECT COUNT(*) FROM aerolineas) AS aerolineas,
    (SELECT COUNT(*) FROM vuelos) AS vuelos,
    (SELECT COUNT(*) FROM promociones) AS promociones,
    (SELECT COUNT(*) FROM reservas) AS reservas,
    (SELECT COUNT(*) FROM pasajeros) AS pasajeros,
    (SELECT COUNT(*) FROM metodos_pago) AS metodos_pago;
