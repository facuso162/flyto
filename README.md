# Flyto

Flyto es una aplicacion PHP 8 sin framework, organizada como un monolito modular. El proyecto separa claramente la capa publica de HTML, la capa API JSON y la logica interna por modulos.

## Arquitectura general

La estructura del proyecto sigue este flujo:

1. `public/index.php` es el punto de entrada unico.
2. Las rutas publicas GET renderizan vistas completas con layout compartido.
3. Las rutas API se registran por modulo y se resuelven con `app/router.php`.
4. Cada request entra al `Container`, que arma las dependencias necesarias.
5. El controlador valida permisos, transforma datos y delega la logica al servicio.
6. El servicio aplica reglas de negocio y usa el repositorio para persistir o consultar en MySQL.
7. La respuesta vuelve como HTML en las paginas publicas o como JSON en la API.

En otras palabras, el proyecto mezcla un enfoque MVC clasico con una capa de servicios y repositorios, mas DTOs y validadores para evitar que el controlador concentre logica de negocio.

## Estructura por capas

### `public/`

Contiene el punto de entrada web y los assets compilados.

- `index.php`: resuelve paginas publicas, carga modulos y arma el container.
- `assets/css`: estilos generados con Tailwind.
- `assets/js`: JavaScript de la app.

### `app/shared/`

Contiene piezas reutilizables por todo el sistema.

- `config/env.php`: carga variables desde `.env` o `.env.example`.
- `database/database.php`: crea la conexion PDO a MySQL.
- `http/`: excepciones y respuestas JSON.
- `views/layouts/`: layouts compartidos para las paginas publicas.
- `services/email.service.php`: servicio base de envio de correo.

### Modulos funcionales

Cada modulo sigue el mismo patron de carpetas:

- `routes.php`: define rutas del modulo.
- `controllers/`: reciben la request y devuelven respuesta.
- `services/`: contienen la logica de negocio.
- `repositories/`: acceden a la base de datos.
- `models/`: representan entidades del dominio.
- `dtos/`: objetos de transferencia para crear o editar datos.
- `validators/`: validan y normalizan input.
- `views/`: vistas HTML o componentes del modulo.

Los modulos visibles en este repo son:

- `auth`: autenticacion, registro, confirmacion de cuenta, login y logout.
- `contacto`: formulario y envio de mensajes.
- `novedades`: listado publico y panel administrativo de novedades.
- `faq`, `home`, `perfil`, `vuelos`: paginas y componentes de interfaz.

## Como funciona la API

Las rutas de API se registran por modulo, por ejemplo `app/auth/routes.php` y `app/novedades/routes.php`. El router central acepta un prefijo por modulo y permite parametros en la URL.

Ejemplo de flujo real:

1. La request entra a `/api/novedades/crear`.
2. `app/router.php` encuentra la ruta y resuelve el controlador.
3. `NovedadController` ejecuta el middleware de admin.
4. `NovedadValidator` valida y arma el DTO.
5. `NovedadService` crea o modifica la entidad.
6. `NovedadRepository` persiste en MySQL con PDO.
7. `JsonResponse` devuelve el JSON final.

Las respuestas estan centralizadas en `app/shared/http/json-response.php`, que distingue entre exito, error y excepciones de negocio.

## Modulo `novedades`

Este modulo es el mejor ejemplo del estilo del proyecto porque ya implementa un CRUD completo.

### Rutas

- `GET /api/novedades/ultimas`
- `GET /api/novedades/vigentes`
- `GET /api/novedades/todas`
- `POST /api/novedades/crear`
- `POST /api/novedades/editar`
- `POST /api/novedades/borrar`

### Piezas principales

- `controllers/novedad.controller.php`: coordina middleware, validacion y respuestas JSON.
- `services/novedad.service.php`: crea, edita, borra y consulta novedades.
- `repositories/novedad.repository.php`: implementa SQL directo con PDO.
- `models/novedad.model.php`: representa la entidad y serializa a array.
- `dtos/crear-novedad.dto.php` y `dtos/editar-novedad.dto.php`: transportan datos validados.
- `validators/novedad.validator.php`: controla presencia, tipo, longitud y fechas.

### Reglas importantes del dominio

- `titulo` es obligatorio y tiene maximo de 160 caracteres.
- `texto` es obligatorio y tiene maximo de 2000 caracteres.
- `categoria` es obligatoria y tiene maximo de 120 caracteres.
- `fechaExpiracion` debe ser valida y futura.
- `getVigentes()` filtra por `fechaExpiracion > NOW()`.
- `estado()` devuelve `vigente` o `expirada` segun la fecha de expiracion.

## Persistencia

La base de datos es MySQL y la conexion se crea en `app/shared/database/database.php`.

El esquema vive en `database/creacion-tablas.sql` y los datos semilla en `database/data-insert.sql`.

La tabla `novedades` maneja:

- `id`
- `titulo`
- `categoria`
- `texto`
- `fechaPublicacion`
- `fechaExpiracion`

## Frontend y renderizado

La capa visual usa PHP server-side rendering con layouts compartidos y Tailwind CSS.

- `public/assets/css/input.css` es la entrada de Tailwind.
- `public/assets/css/app.css` es el CSS compilado.
- `app/shared/views/layouts/` contiene la plantilla base.
- Las vistas por modulo viven en `app/<modulo>/views/pages` y `components`.

Las paginas publicas no pasan por el router JSON; se resuelven directamente desde `public/index.php` con un mapa de rutas estaticas.

## Dependencias

- PHP 8 o superior.
- `phpmailer/phpmailer` para correos.
- Node.js y Tailwind para compilar estilos.
- MySQL para persistencia.

## Desarrollo local

Instalacion base:

1. Instalar dependencias PHP con Composer.
2. Instalar dependencias de frontend con npm.
3. Configurar variables de entorno si el proyecto lo requiere.
4. Crear la base de datos `flyto` y ejecutar `database/creacion-tablas.sql`.
5. Compilar estilos con Tailwind.
6. Servir `public/` como document root.

Comandos utiles:

- `npm run dev:css`
- `npm run build:css`

## Convenciones del proyecto

- El controlador no accede a la base de datos directamente.
- La validacion de input se hace antes de entrar al servicio.
- El repositorio se limita a SQL y mapeo de filas.
- Las entidades exponen `toArray()` para serializacion JSON.
- Los errores de negocio se representan con `HttpException` y llegan al cliente como JSON.

## Como extender el proyecto

Para sumar un nuevo CRUD, el patron esperado es:

1. Crear `models`, `dtos`, `validators`, `repositories`, `services` y `controllers` del nuevo modulo.
2. Registrar las rutas en `routes.php`.
3. Enlazar dependencias en `app/container.php` y cargar archivos en `public/index.php`.
4. Reutilizar `JsonResponse` y `HttpException` para devolver errores consistentes.
5. Si hay panel administrativo, protegerlo con middlewares existentes o uno nuevo.
