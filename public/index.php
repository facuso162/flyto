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

use App\Auth\Services\EmailService;
use App\Auth\Services\ConfirmacionUsuarioEmailService;
use App\Auth\Services\RegisterUsuarioService;
use App\Auth\Services\ConfirmarUsuarioService;
use App\Auth\Services\LoginUsuarioService;
use App\Auth\Services\LogoutUsuarioService;

use App\Auth\Controllers\RegisterUsuarioController;
use App\Auth\Controllers\ConfirmarUsuarioController;
use App\Auth\Controllers\LoginUsuarioController;
use App\Auth\Controllers\LogoutUsuarioController;

$autoload = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;
}

require_once __DIR__ . '/../app/container.php';
require_once __DIR__ . '/../app/router.php';

require_once __DIR__ . '/../app/shared/config/env.php';
require_once __DIR__ . '/../app/shared/database/database.php';

Env::load(__DIR__ . '/../.env.example');

require_once __DIR__ . '/../app/auth/routes.php';

require_once __DIR__ . '/../app/auth/repositories/usuario.repository.php';
require_once __DIR__ . '/../app/auth/repositories/tipo-usuario.repository.php';

require_once __DIR__ . '/../app/auth/services/session.service.php';

require_once __DIR__ . '/../app/auth/services/email.service.php';
require_once __DIR__ . '/../app/auth/services/confirmacion-usuario-email.service.php';
require_once __DIR__ . '/../app/auth/services/register-usuario.service.php';
require_once __DIR__ . '/../app/auth/services/confirmar-usuario.service.php';
require_once __DIR__ . '/../app/auth/services/login-usuario.service.php';
require_once __DIR__ . '/../app/auth/services/logout-usuario.service.php';

require_once __DIR__ . '/../app/auth/controllers/register-usuario.controller.php';
require_once __DIR__ . '/../app/auth/controllers/confirmar-usuario.controller.php';
require_once __DIR__ . '/../app/auth/controllers/login-usuario.controller.php';
require_once __DIR__ . '/../app/auth/controllers/logout-usuario.controller.php';

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

// Instanciar router

$router = new Router();

$router->registerModule(require __DIR__ . '/../app/auth/routes.php');

$router->resolve(
    $_SERVER['REQUEST_METHOD'], 
    $_SERVER['REQUEST_URI'], 
    $container
);
