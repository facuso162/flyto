<?php 

// echo "Proyecto funcionando <br><br>";

// Dependencias globales

use App\Container;
use App\Router;

use App\Shared\Config\Env;
use App\Shared\Database\Database;

// Modulo Auth

use App\Auth\Repositories\UsuarioRepository;
use App\Auth\Repositories\TipoUsuarioRepository;

use App\Auth\Services\SessionService;

use App\Shared\Services\EmailService;
use App\Auth\Services\ConfirmacionUsuarioEmailService;
use App\Auth\Services\RegisterUsuarioService;
use App\Auth\Services\ConfirmarUsuarioService;
use App\Auth\Services\LoginUsuarioService;
use App\Auth\Services\LogoutUsuarioService;

use App\Auth\Controllers\RegisterUsuarioController;
use App\Auth\Controllers\ConfirmarUsuarioController;
use App\Auth\Controllers\LoginUsuarioController;
use App\Auth\Controllers\LogoutUsuarioController;

// Modulo Contacto

use App\Contacto\Services\ContactoEmailService;
use App\Contacto\Services\EnviarMensajeService;
use App\Contacto\Controllers\ContactoPageController;
use App\Contacto\Controllers\EnviarMensajeActionController;

// Modulo Novedades

use App\Novedades\Repositories\NovedadRepository;
use App\Novedades\Services\NovedadService;
use App\Novedades\Controllers\NovedadController;

// Modulo Vuelos

use App\Vuelos\Repositories\VueloRepository;
use App\Vuelos\Services\VueloService;
use App\Vuelos\Dtos\BuscarVuelosDto;
use App\Vuelos\Validators\BuscarVueloValidator;
use App\Vuelos\Controllers\VueloController;

$autoload = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;
}

require_once __DIR__ . '/../app/container.php';
require_once __DIR__ . '/../app/router.php';

require_once __DIR__ . '/../app/shared/config/env.php';
require_once __DIR__ . '/../app/shared/database/database.php';
require_once __DIR__ . '/../app/contacto/controllers/contacto-page.controller.php';
require_once __DIR__ . '/../app/novedades/repositories/novedad.repository.php';
require_once __DIR__ . '/../app/novedades/services/novedad.service.php';
require_once __DIR__ . '/../app/vuelos/repositories/vuelo.repository.php';
require_once __DIR__ . '/../app/vuelos/services/vuelo.service.php';
require_once __DIR__ . '/../app/vuelos/dtos/buscar-vuelos.dto.php';
require_once __DIR__ . '/../app/vuelos/validators/buscar-vuelo.validator.php';

Env::load(__DIR__ . '/../.env.example');

function createNovedadService(): NovedadService
{
    return new NovedadService(
        new NovedadRepository(Database::getConnection())
    );
}

function loadPublicNovedades(callable $loader): array
{
    try {
        return array_map(
            fn ($novedad) => $novedad->toArray(),
            $loader(createNovedadService())
        );
    } catch (Throwable) {
        return [];
    }
}

function createVueloService(): VueloService
{
    return new VueloService(new VueloRepository(Database::getConnection()));
}

function defaultBusquedaVuelosQuery(): array
{
    return [
        'origen' => '1',
        'destino' => '2',
        'fechaSalida' => '2026-08-15',
        'cantidadPasajeros' => '1',
        'orden' => 'precio',
    ];
}

function loadPublicBusquedaVuelos(array $query): array
{
    $data = $query === [] ? defaultBusquedaVuelosQuery() : $query;
    BuscarVueloValidator::validate($data);

    return createVueloService()->buscar(BuscarVuelosDto::fromArray($data));
}

function renderPublicPage(string $viewPath, string $title, string $basePath, string $currentPath, array $viewData = []): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $pageTitle = $title;
    $currentUser = $_SESSION['usuario'] ?? null;
    $isAuthenticated = $currentUser !== null;

    ob_start();
    extract($viewData, EXTR_SKIP);
    require $viewPath;
    $content = ob_get_clean();

    require __DIR__ . '/../app/shared/views/layouts/public.layout.php';
}

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = $scriptDir === '/' ? '' : rtrim($scriptDir, '/');

if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
    $requestPath = substr($requestPath, strlen($basePath)) ?: '/';
}

$requestPath = $requestPath !== '/' ? rtrim($requestPath, '/') : '/';

function requireAuthenticatedPublicUser(string $basePath): array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $usuario = $_SESSION['usuario'] ?? null;

    if (!is_array($usuario)) {
        header('Location: ' . ($basePath ?: '') . '/login');
        exit;
    }

    return $usuario;
}

$publicRoutes = [
    '/' => [
        'view' => __DIR__ . '/../app/home/views/pages/home.page.php',
        'title' => 'Flyto - Reservas de vuelos',
        'data' => fn () => [
            'ultimasNovedades' => loadPublicNovedades(
                fn (NovedadService $service) => $service->getUltimas()
            ),
        ],
    ],
    '/login' => [
        'view' => __DIR__ . '/../app/auth/views/pages/login.page.php',
        'title' => 'Ingresar - Flyto',
    ],
    '/registro' => [
        'view' => __DIR__ . '/../app/auth/views/pages/registro.page.php',
        'title' => 'Registrarse - Flyto',
    ],
    '/registro/confirmacion-enviada' => [
        'view' => __DIR__ . '/../app/auth/views/pages/registro-confirmacion-enviada.page.php',
        'title' => 'Confirmacion enviada - Flyto',
    ],
    '/cuenta-confirmada' => [
        'view' => __DIR__ . '/../app/auth/views/pages/cuenta-confirmada.page.php',
        'title' => 'Cuenta confirmada - Flyto',
    ],
    '/recuperar-contrasena' => [
        'view' => __DIR__ . '/../app/auth/views/pages/recuperar-contrasena.page.php',
        'title' => 'Recuperar contrasena - Flyto',
    ],
    '/recuperar-contrasena/codigo' => [
        'view' => __DIR__ . '/../app/auth/views/pages/recuperar-contrasena-token.page.php',
        'title' => 'Codigo de recuperacion - Flyto',
    ],
    '/recuperar-contrasena/cambiar' => [
        'view' => __DIR__ . '/../app/auth/views/pages/recuperar-contrasena-cambiar.page.php',
        'title' => 'Cambiar contrasena - Flyto',
    ],
    '/novedades' => [
        'view' => __DIR__ . '/../app/novedades/views/pages/novedades.page.php',
        'title' => 'Novedades - Flyto',
        'data' => fn () => [
            'novedades' => loadPublicNovedades(
                fn (NovedadService $service) => $service->getVigentes()
            ),
        ],
    ],
    '/faq' => [
        'view' => __DIR__ . '/../app/faq/views/pages/faq.page.php',
        'title' => 'Preguntas frecuentes - Flyto',
    ],
    '/contacto' => [
        'controller' => ContactoPageController::class,
        'action' => 'show',
    ],
    '/vuelos/buscar' => [
        'view' => __DIR__ . '/../app/vuelos/views/pages/buscar-vuelos.page.php',
        'title' => 'Buscar vuelos - Flyto',
        'data' => fn () => [
            'resultadoBusqueda' => loadPublicBusquedaVuelos($_GET),
        ],
    ],
    '/mi-perfil' => [
        'view' => __DIR__ . '/../app/perfil/views/pages/mi-perfil.page.php',
        'title' => 'Mi perfil - Flyto',
        'data' => fn () => [
            'usuario' => requireAuthenticatedPublicUser($basePath),
        ],
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($publicRoutes[$requestPath])) {
    if (isset($publicRoutes[$requestPath]['controller'], $publicRoutes[$requestPath]['action'])) {
        $controllerClass = $publicRoutes[$requestPath]['controller'];
        $controllerAction = $publicRoutes[$requestPath]['action'];
        $controller = new $controllerClass();
        $controller->{$controllerAction}();
        return;
    }

    $viewData = isset($publicRoutes[$requestPath]['data'])
        ? $publicRoutes[$requestPath]['data']()
        : [];

    renderPublicPage($publicRoutes[$requestPath]['view'], $publicRoutes[$requestPath]['title'], $basePath, $requestPath, $viewData);
    return;
}

require_once __DIR__ . '/../app/auth/routes.php';
require_once __DIR__ . '/../app/contacto/routes.php';
require_once __DIR__ . '/../app/novedades/routes.php';
require_once __DIR__ . '/../app/vuelos/routes.php';

require_once __DIR__ . '/../app/auth/repositories/usuario.repository.php';
require_once __DIR__ . '/../app/auth/repositories/tipo-usuario.repository.php';
require_once __DIR__ . '/../app/novedades/repositories/novedad.repository.php';
require_once __DIR__ . '/../app/vuelos/repositories/vuelo.repository.php';

require_once __DIR__ . '/../app/auth/services/session.service.php';

require_once __DIR__ . '/../app/shared/services/email.service.php';
require_once __DIR__ . '/../app/auth/services/confirmacion-usuario-email.service.php';
require_once __DIR__ . '/../app/auth/services/register-usuario.service.php';
require_once __DIR__ . '/../app/auth/services/confirmar-usuario.service.php';
require_once __DIR__ . '/../app/auth/services/login-usuario.service.php';
require_once __DIR__ . '/../app/auth/services/logout-usuario.service.php';

require_once __DIR__ . '/../app/auth/controllers/register-usuario.controller.php';
require_once __DIR__ . '/../app/auth/controllers/confirmar-usuario.controller.php';
require_once __DIR__ . '/../app/auth/controllers/login-usuario.controller.php';
require_once __DIR__ . '/../app/auth/controllers/logout-usuario.controller.php';

require_once __DIR__ . '/../app/contacto/services/contacto-email.service.php';
require_once __DIR__ . '/../app/contacto/services/enviar-mensaje.service.php';
require_once __DIR__ . '/../app/contacto/controllers/enviar-mensaje-action.controller.php';

require_once __DIR__ . '/../app/novedades/services/novedad.service.php';
require_once __DIR__ . '/../app/novedades/controllers/novedad.controller.php';

require_once __DIR__ . '/../app/vuelos/dtos/buscar-vuelos.dto.php';
require_once __DIR__ . '/../app/vuelos/validators/buscar-vuelo.validator.php';
require_once __DIR__ . '/../app/vuelos/services/vuelo.service.php';
require_once __DIR__ . '/../app/vuelos/controllers/vuelo.controller.php';

$container = new Container();

$container->singleton(Database::class, function () {
    return Database::getConnection();
});

// Registrar dependencias de modulo Auth en container

$container->scoped(UsuarioRepository::class, function ($c) {
    return new UsuarioRepository($c->get(Database::class));
});

$container->scoped(TipoUsuarioRepository::class, function ($c) {
    return new TipoUsuarioRepository($c->get(Database::class));
});

$container->singleton(SessionService::class, function () {
    return new SessionService();
});

$container->scoped(EmailService::class, function () {
    return new EmailService();
});

$container->scoped(ConfirmacionUsuarioEmailService::class, function ($c) {
    return new ConfirmacionUsuarioEmailService($c->get(EmailService::class));
});

$container->scoped(RegisterUsuarioService::class, function ($c) {
    return new RegisterUsuarioService(
        $c->get(UsuarioRepository::class),
        $c->get(TipoUsuarioRepository::class),
        $c->get(ConfirmacionUsuarioEmailService::class)
    );
});

$container->scoped(RegisterUsuarioController::class, function ($c) {
    return new RegisterUsuarioController(
        $c->get(RegisterUsuarioService::class),
        $c->get(SessionService::class)
    );
});


$container->scoped(ConfirmarUsuarioService::class, function ($c) {
    return new ConfirmarUsuarioService($c->get(UsuarioRepository::class));
});

$container->scoped(ConfirmarUsuarioController::class, function ($c) {
    return new ConfirmarUsuarioController(
        $c->get(ConfirmarUsuarioService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(LoginUsuarioService::class, function ($c) {
    return new LoginUsuarioService(
        $c->get(UsuarioRepository::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(LoginUsuarioController::class, function ($c) {
    return new LoginUsuarioController(
        $c->get(LoginUsuarioService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(LogoutUsuarioService::class, function ($c) {
    return new LogoutUsuarioService($c->get(SessionService::class));
});

$container->scoped(LogoutUsuarioController::class, function ($c) {
    return new LogoutUsuarioController(
        $c->get(LogoutUsuarioService::class),
        $c->get(SessionService::class)
    );
});

// Registrar dependencias de modulo Contacto en container

$container->scoped(ContactoEmailService::class, function ($c) {
    return new ContactoEmailService($c->get(EmailService::class));
});

$container->scoped(EnviarMensajeService::class, function ($c) {
    return new EnviarMensajeService($c->get(ContactoEmailService::class));
});

$container->scoped(EnviarMensajeActionController::class, function ($c) {
    return new EnviarMensajeActionController($c->get(EnviarMensajeService::class));
});

// Registrar dependencias de modulo Novedades en container

$container->scoped(NovedadRepository::class, function ($c) {
    return new NovedadRepository($c->get(Database::class));
});

$container->scoped(NovedadService::class, function ($c) {
    return new NovedadService($c->get(NovedadRepository::class));
});

$container->scoped(NovedadController::class, function ($c) {
    return new NovedadController(
        $c->get(NovedadService::class),
        $c->get(SessionService::class)
    );
});

// Registrar dependencias de modulo Vuelos en container

$container->scoped(VueloRepository::class, function ($c) {
    return new VueloRepository($c->get(Database::class));
});

$container->scoped(VueloService::class, function ($c) {
    return new VueloService($c->get(VueloRepository::class));
});

$container->scoped(VueloController::class, function ($c) {
    return new VueloController($c->get(VueloService::class));
});

// Instanciar router

$router = new Router();

$router->registerModule(require __DIR__ . '/../app/auth/routes.php');
$router->registerModule(require __DIR__ . '/../app/contacto/routes.php');
$router->registerModule(require __DIR__ . '/../app/novedades/routes.php');
$router->registerModule(require __DIR__ . '/../app/vuelos/routes.php');

$normalizedUri = $requestPath;
$requestQuery = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

if ($requestQuery !== null && $requestQuery !== false && $requestQuery !== '') {
    $normalizedUri .= '?' . $requestQuery;
}

$router->resolve(
    $_SERVER['REQUEST_METHOD'], 
    $normalizedUri, 
    $container
);
