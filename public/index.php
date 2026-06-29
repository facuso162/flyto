<?php

use App\Auth\Controllers\ConfirmarUsuarioActionController;
use App\Auth\Controllers\EnviarTokenRecuperacionActionController;
use App\Auth\Controllers\LoginPageController;
use App\Auth\Controllers\LoginUsuarioActionController;
use App\Auth\Controllers\LogoutUsuarioActionController;
use App\Auth\Controllers\RegisterUsuarioActionController;
use App\Auth\Controllers\RecuperarContrasenaPageController;
use App\Auth\Controllers\RecuperarContrasenaTokenPageController;
use App\Auth\Controllers\RegistroPageController;
use App\Admin\Controllers\AdminDashboardPageController;
use App\Aerolineas\Controllers\BorrarAerolineaActionController;
use App\Aerolineas\Controllers\CrearAerolineaActionController;
use App\Aerolineas\Controllers\CrearAerolineaPageController;
use App\Aerolineas\Controllers\EditarAerolineaActionController;
use App\Aerolineas\Controllers\EditarAerolineaPageController;
use App\Aerolineas\Controllers\ListadoAerolineasPageController;
use App\Aerolineas\Repositories\AerolineaRepository;
use App\Aerolineas\Services\AerolineaService;
use App\Auth\Middlewares\AdminMiddleware;
use App\Auth\Middlewares\AuthMiddleware;
use App\Auth\Middlewares\CeoMiddleware;
use App\Auth\Middlewares\GuestMiddleware;
use App\Auth\Repositories\TipoUsuarioRepository;
use App\Auth\Repositories\UsuarioRepository;
use App\Auth\Services\ConfirmacionUsuarioEmailService;
use App\Auth\Services\ConfirmarUsuarioService;
use App\Auth\Services\EnviarTokenRecuperacionService;
use App\Auth\Services\LoginUsuarioService;
use App\Auth\Services\LogoutUsuarioService;
use App\Auth\Services\RegisterUsuarioService;
use App\Auth\Services\SessionService;
use App\Auth\Services\TokenRecuperacionEmailService;
use App\Contacto\Controllers\ContactoPageController;
use App\Contacto\Controllers\EnviarMensajeActionController;
use App\Contacto\Services\ContactoEmailService;
use App\Contacto\Services\EnviarMensajeService;
use App\Ciudades\Repositories\CiudadRepository;
use App\Ciudades\Services\CiudadService;
use App\Ceo\Controllers\CeoDashboardPageController;
use App\Container;
use App\Home\Controllers\HomePageController;
use App\Novedades\Controllers\AdminNovedadesPageController;
use App\Novedades\Controllers\BorrarNovedadActionController;
use App\Novedades\Controllers\CrearNovedadActionController;
use App\Novedades\Controllers\CrearNovedadPageController;
use App\Novedades\Controllers\EditarNovedadActionController;
use App\Novedades\Controllers\EditarNovedadPageController;
use App\Novedades\Controllers\NovedadesPageController;
use App\Novedades\Repositories\NovedadRepository;
use App\Novedades\Services\NovedadService;
use App\Paises\Repositories\PaisRepository;
use App\Paises\Services\PaisService;
use App\Perfil\Controllers\MiPerfilPageController;
use App\Promociones\Controllers\AprobarPromocionActionController;
use App\Promociones\Controllers\BorrarPromocionActionController;
use App\Promociones\Controllers\CrearPromocionActionController;
use App\Promociones\Controllers\CrearPromocionPageController;
use App\Promociones\Controllers\DenegarPromocionActionController;
use App\Promociones\Controllers\DesactivarPromocionActionController;
use App\Promociones\Controllers\EditarPromocionActionController;
use App\Promociones\Controllers\EditarPromocionPageController;
use App\Promociones\Controllers\ListadoPromocionesPageController;
use App\Promociones\Controllers\SolicitarActivacionActionController;
use App\Promociones\Controllers\SolicitarActivacionPageController;
use App\Promociones\Controllers\SolicitudesPromocionesPageController;
use App\Promociones\Repositories\PromocionRepository;
use App\Promociones\Services\PromocionService;
use App\Reportes\Controllers\AdminListadoReportesPageController;
use App\Reportes\Controllers\AdminReporteCeosPageController;
use App\Reportes\Controllers\AdminReporteVentasPageController;
use App\Reportes\Controllers\AdminReporteVuelosPageController;
use App\Reportes\Controllers\ListadoReportesPageController;
use App\Reportes\Controllers\ReporteOcupacionPageController;
use App\Reportes\Controllers\ReporteVentasPageController;
use App\Reportes\Repositories\ReporteRepository;
use App\Reportes\Services\ReporteService;
use App\Reservas\Controllers\CancelarReservaActionController;
use App\Reservas\Controllers\CrearReservaActionController;
use App\Reservas\Controllers\ConfirmacionPageController;
use App\Reservas\Controllers\GuardarPasajerosActionController;
use App\Reservas\Controllers\PagoPageController;
use App\Reservas\Controllers\PasajerosPageController;
use App\Reservas\Repositories\ReservaRepository;
use App\Reservas\Services\ReservaService;
use App\Reservas\Controllers\MisReservasPageController;
use App\Reservas\Controllers\ReservaDetallePageController;
use App\Router;
use App\Shared\Config\Env;
use App\Shared\Database\Database;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;
use App\Shared\Services\EmailService;
use App\Usuarios\Controllers\CrearCeoActionController;
use App\Usuarios\Controllers\CrearCeoPageController;
use App\Usuarios\Controllers\ListadoCeosPageController;
use App\Usuarios\Repositories\UsuarioRepository as PanelUsuarioRepository;
use App\Usuarios\Services\UsuarioService as PanelUsuarioService;
use App\Vuelos\Controllers\BuscarVuelosPageController;
use App\Vuelos\Controllers\BorrarVueloActionController;
use App\Vuelos\Controllers\CrearVueloActionController;
use App\Vuelos\Controllers\CrearVueloPageController;
use App\Vuelos\Controllers\EditarVueloActionController;
use App\Vuelos\Controllers\EditarVueloPageController;
use App\Vuelos\Controllers\ListadoVuelosPageController;
use App\Vuelos\Controllers\VueloController;
use App\Vuelos\Repositories\VueloRepository;
use App\Vuelos\Services\VueloService;

$autoload = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;
}

require_once __DIR__ . '/../app/container.php';
require_once __DIR__ . '/../app/router.php';

require_once __DIR__ . '/../app/shared/config/env.php';
require_once __DIR__ . '/../app/shared/database/database.php';
require_once __DIR__ . '/../app/shared/http/flash.php';
require_once __DIR__ . '/../app/shared/http/http-exception.php';
require_once __DIR__ . '/../app/shared/http/redirect-response.php';
require_once __DIR__ . '/../app/shared/http/view-response.php';
require_once __DIR__ . '/../app/shared/services/email.service.php';

require_once __DIR__ . '/../app/auth/middlewares/auth.middleware.php';
require_once __DIR__ . '/../app/auth/middlewares/admin.middleware.php';
require_once __DIR__ . '/../app/auth/middlewares/ceo.middleware.php';
require_once __DIR__ . '/../app/auth/middlewares/guest.middleware.php';
require_once __DIR__ . '/../app/auth/repositories/usuario.repository.php';
require_once __DIR__ . '/../app/auth/repositories/tipo-usuario.repository.php';
require_once __DIR__ . '/../app/auth/services/session.service.php';
require_once __DIR__ . '/../app/auth/services/confirmacion-usuario-email.service.php';
require_once __DIR__ . '/../app/auth/services/token-recuperacion-email.service.php';
require_once __DIR__ . '/../app/auth/services/enviar-token-recuperacion.service.php';
require_once __DIR__ . '/../app/auth/services/register-usuario.service.php';
require_once __DIR__ . '/../app/auth/services/confirmar-usuario.service.php';
require_once __DIR__ . '/../app/auth/services/login-usuario.service.php';
require_once __DIR__ . '/../app/auth/services/logout-usuario.service.php';
require_once __DIR__ . '/../app/auth/controllers/login-page.controller.php';
require_once __DIR__ . '/../app/auth/controllers/registro-page.controller.php';
require_once __DIR__ . '/../app/auth/controllers/recuperar-contrasena-page.controller.php';
require_once __DIR__ . '/../app/auth/controllers/recuperar-contrasena-token-page.controller.php';
require_once __DIR__ . '/../app/auth/controllers/confirmar-usuario-action.controller.php';
require_once __DIR__ . '/../app/auth/controllers/enviar-token-recuperacion-action.controller.php';
require_once __DIR__ . '/../app/auth/controllers/login-usuario-action.controller.php';
require_once __DIR__ . '/../app/auth/controllers/register-usuario-action.controller.php';
require_once __DIR__ . '/../app/auth/controllers/logout-usuario-action.controller.php';

require_once __DIR__ . '/../app/contacto/services/contacto-email.service.php';
require_once __DIR__ . '/../app/contacto/services/enviar-mensaje.service.php';
require_once __DIR__ . '/../app/contacto/controllers/contacto-page.controller.php';
require_once __DIR__ . '/../app/contacto/controllers/enviar-mensaje-action.controller.php';

require_once __DIR__ . '/../app/paises/repositories/pais.repository.php';
require_once __DIR__ . '/../app/paises/services/pais.service.php';

require_once __DIR__ . '/../app/aerolineas/repositories/aerolinea.repository.php';
require_once __DIR__ . '/../app/aerolineas/services/aerolinea.service.php';
require_once __DIR__ . '/../app/aerolineas/controllers/borrar-aerolinea-action.controller.php';
require_once __DIR__ . '/../app/aerolineas/controllers/crear-aerolinea-action.controller.php';
require_once __DIR__ . '/../app/aerolineas/controllers/crear-aerolinea-page.controller.php';
require_once __DIR__ . '/../app/aerolineas/controllers/editar-aerolinea-action.controller.php';
require_once __DIR__ . '/../app/aerolineas/controllers/editar-aerolinea-page.controller.php';
require_once __DIR__ . '/../app/aerolineas/controllers/listado-aerolineas-page.controller.php';

require_once __DIR__ . '/../app/ciudades/repositories/ciudad.repository.php';
require_once __DIR__ . '/../app/ciudades/services/ciudad.service.php';

require_once __DIR__ . '/../app/home/controllers/home-page.controller.php';

require_once __DIR__ . '/../app/promociones/repositories/promocion.repository.php';
require_once __DIR__ . '/../app/promociones/services/promocion.service.php';
require_once __DIR__ . '/../app/promociones/controllers/borrar-promocion-action.controller.php';
require_once __DIR__ . '/../app/promociones/controllers/crear-promocion-action.controller.php';
require_once __DIR__ . '/../app/promociones/controllers/crear-promocion-page.controller.php';
require_once __DIR__ . '/../app/promociones/controllers/desactivar-promocion-action.controller.php';
require_once __DIR__ . '/../app/promociones/controllers/editar-promocion-action.controller.php';
require_once __DIR__ . '/../app/promociones/controllers/editar-promocion-page.controller.php';
require_once __DIR__ . '/../app/promociones/controllers/listado-promociones-page.controller.php';
require_once __DIR__ . '/../app/promociones/controllers/solicitar-activacion-action.controller.php';
require_once __DIR__ . '/../app/promociones/controllers/solicitar-activacion-page.controller.php';
require_once __DIR__ . '/../app/promociones/controllers/solicitudes-promociones-page.controller.php';
require_once __DIR__ . '/../app/promociones/controllers/aprobar-promocion-action.controller.php';
require_once __DIR__ . '/../app/promociones/controllers/denegar-promocion-action.controller.php';

require_once __DIR__ . '/../app/usuarios/repositories/usuario.repository.php';
require_once __DIR__ . '/../app/usuarios/services/usuario.service.php';
require_once __DIR__ . '/../app/usuarios/controllers/crear-ceo-action.controller.php';
require_once __DIR__ . '/../app/usuarios/controllers/crear-ceo-page.controller.php';
require_once __DIR__ . '/../app/usuarios/controllers/listado-ceos-page.controller.php';

require_once __DIR__ . '/../app/admin/controllers/admin-dashboard-page.controller.php';

require_once __DIR__ . '/../app/ceo/controllers/ceo-dashboard-page.controller.php';
require_once __DIR__ . '/../app/reportes/repositories/reporte.repository.php';
require_once __DIR__ . '/../app/reportes/services/reporte.service.php';
require_once __DIR__ . '/../app/reportes/controllers/admin-listado-reportes-page.controller.php';
require_once __DIR__ . '/../app/reportes/controllers/admin-reporte-ceos-page.controller.php';
require_once __DIR__ . '/../app/reportes/controllers/admin-reporte-ventas-page.controller.php';
require_once __DIR__ . '/../app/reportes/controllers/admin-reporte-vuelos-page.controller.php';
require_once __DIR__ . '/../app/reportes/controllers/listado-reportes-page.controller.php';
require_once __DIR__ . '/../app/reportes/controllers/reporte-ventas-page.controller.php';
require_once __DIR__ . '/../app/reportes/controllers/reporte-ocupacion-page.controller.php';

require_once __DIR__ . '/../app/novedades/repositories/novedad.repository.php';
require_once __DIR__ . '/../app/novedades/services/novedad.service.php';
require_once __DIR__ . '/../app/novedades/controllers/novedades-page.controller.php';
require_once __DIR__ . '/../app/novedades/controllers/admin-novedades-page.controller.php';
require_once __DIR__ . '/../app/novedades/controllers/crear-novedad-page.controller.php';
require_once __DIR__ . '/../app/novedades/controllers/crear-novedad-action.controller.php';
require_once __DIR__ . '/../app/novedades/controllers/editar-novedad-page.controller.php';
require_once __DIR__ . '/../app/novedades/controllers/editar-novedad-action.controller.php';
require_once __DIR__ . '/../app/novedades/controllers/borrar-novedad-action.controller.php';

require_once __DIR__ . '/../app/perfil/controllers/mi-perfil-page.controller.php';

require_once __DIR__ . '/../app/reservas/repositories/reserva.repository.php';
require_once __DIR__ . '/../app/reservas/services/reserva.service.php';
require_once __DIR__ . '/../app/reservas/controllers/cancelar-reserva-action.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/crear-reserva-action.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/confirmacion-page.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/guardar-pasajeros-action.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/pago-page.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/pasajeros-page.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/mis-reservas-page.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/reserva-detalle-page.controller.php';

require_once __DIR__ . '/../app/vuelos/repositories/vuelo.repository.php';
require_once __DIR__ . '/../app/vuelos/services/vuelo.service.php';
require_once __DIR__ . '/../app/vuelos/controllers/vuelo.controller.php';
require_once __DIR__ . '/../app/vuelos/controllers/buscar-vuelos-page.controller.php';
require_once __DIR__ . '/../app/vuelos/controllers/borrar-vuelo-action.controller.php';
require_once __DIR__ . '/../app/vuelos/controllers/crear-vuelo-action.controller.php';
require_once __DIR__ . '/../app/vuelos/controllers/crear-vuelo-page.controller.php';
require_once __DIR__ . '/../app/vuelos/controllers/editar-vuelo-action.controller.php';
require_once __DIR__ . '/../app/vuelos/controllers/editar-vuelo-page.controller.php';
require_once __DIR__ . '/../app/vuelos/controllers/listado-vuelos-page.controller.php';

Env::load(__DIR__ . '/../.env');

$container = new Container();

$container->singleton(Database::class, function () {
    return Database::getConnection();
});

$container->singleton(SessionService::class, function () {
    return new SessionService();
});

$container->scoped(ViewResponse::class, function ($c) {
    return new ViewResponse($c->get(SessionService::class));
});

$container->scoped(AuthMiddleware::class, function ($c) {
    return new AuthMiddleware($c->get(SessionService::class));
});

$container->scoped(AdminMiddleware::class, function ($c) {
    return new AdminMiddleware($c->get(SessionService::class));
});

$container->scoped(CeoMiddleware::class, function ($c) {
    return new CeoMiddleware($c->get(SessionService::class));
});

$container->scoped(GuestMiddleware::class, function ($c) {
    return new GuestMiddleware($c->get(SessionService::class));
});

$container->scoped(UsuarioRepository::class, function ($c) {
    return new UsuarioRepository($c->get(Database::class));
});

$container->scoped(TipoUsuarioRepository::class, function ($c) {
    return new TipoUsuarioRepository($c->get(Database::class));
});

$container->scoped(EmailService::class, function () {
    return new EmailService();
});

$container->scoped(ConfirmacionUsuarioEmailService::class, function ($c) {
    return new ConfirmacionUsuarioEmailService($c->get(EmailService::class));
});

$container->scoped(TokenRecuperacionEmailService::class, function ($c) {
    return new TokenRecuperacionEmailService($c->get(EmailService::class));
});

$container->scoped(RegisterUsuarioService::class, function ($c) {
    return new RegisterUsuarioService(
        $c->get(UsuarioRepository::class),
        $c->get(TipoUsuarioRepository::class),
        $c->get(ConfirmacionUsuarioEmailService::class)
    );
});

$container->scoped(EnviarTokenRecuperacionService::class, function ($c) {
    return new EnviarTokenRecuperacionService(
        $c->get(UsuarioRepository::class),
        $c->get(TokenRecuperacionEmailService::class)
    );
});

$container->scoped(ConfirmarUsuarioService::class, function ($c) {
    return new ConfirmarUsuarioService($c->get(UsuarioRepository::class));
});

$container->scoped(LoginUsuarioService::class, function ($c) {
    return new LoginUsuarioService(
        $c->get(UsuarioRepository::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(LogoutUsuarioService::class, function ($c) {
    return new LogoutUsuarioService($c->get(SessionService::class));
});

$container->scoped(LoginPageController::class, function ($c) {
    return new LoginPageController(
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(RegistroPageController::class, function ($c) {
    return new RegistroPageController(
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(RecuperarContrasenaPageController::class, function ($c) {
    return new RecuperarContrasenaPageController(
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(RecuperarContrasenaTokenPageController::class, function ($c) {
    return new RecuperarContrasenaTokenPageController(
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(RegisterUsuarioActionController::class, function ($c) {
    return new RegisterUsuarioActionController(
        $c->get(RegisterUsuarioService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(EnviarTokenRecuperacionActionController::class, function ($c) {
    return new EnviarTokenRecuperacionActionController(
        $c->get(EnviarTokenRecuperacionService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(ConfirmarUsuarioActionController::class, function ($c) {
    return new ConfirmarUsuarioActionController(
        $c->get(ConfirmarUsuarioService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(LoginUsuarioActionController::class, function ($c) {
    return new LoginUsuarioActionController(
        $c->get(LoginUsuarioService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(LogoutUsuarioActionController::class, function ($c) {
    return new LogoutUsuarioActionController(
        $c->get(LogoutUsuarioService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(ContactoEmailService::class, function ($c) {
    return new ContactoEmailService($c->get(EmailService::class));
});

$container->scoped(EnviarMensajeService::class, function ($c) {
    return new EnviarMensajeService($c->get(ContactoEmailService::class));
});

$container->scoped(ContactoPageController::class, function ($c) {
    return new ContactoPageController($c->get(ViewResponse::class));
});

$container->scoped(EnviarMensajeActionController::class, function ($c) {
    return new EnviarMensajeActionController($c->get(EnviarMensajeService::class));
});

$container->scoped(PaisRepository::class, function ($c) {
    return new PaisRepository($c->get(Database::class));
});

$container->scoped(PaisService::class, function ($c) {
    return new PaisService($c->get(PaisRepository::class));
});

$container->scoped(AerolineaRepository::class, function ($c) {
    return new AerolineaRepository($c->get(Database::class));
});

$container->scoped(AerolineaService::class, function ($c) {
    return new AerolineaService(
        $c->get(AerolineaRepository::class),
        $c->get(PaisService::class)
    );
});

$container->scoped(CrearAerolineaPageController::class, function ($c) {
    return new CrearAerolineaPageController(
        $c->get(PaisService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(CrearAerolineaActionController::class, function ($c) {
    return new CrearAerolineaActionController(
        $c->get(AerolineaService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(BorrarAerolineaActionController::class, function ($c) {
    return new BorrarAerolineaActionController(
        $c->get(AerolineaService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(EditarAerolineaPageController::class, function ($c) {
    return new EditarAerolineaPageController(
        $c->get(AerolineaService::class),
        $c->get(PaisService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(EditarAerolineaActionController::class, function ($c) {
    return new EditarAerolineaActionController(
        $c->get(AerolineaService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(ListadoAerolineasPageController::class, function ($c) {
    return new ListadoAerolineasPageController(
        $c->get(AerolineaService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(CiudadRepository::class, function ($c) {
    return new CiudadRepository($c->get(Database::class));
});

$container->scoped(CiudadService::class, function ($c) {
    return new CiudadService($c->get(CiudadRepository::class));
});

$container->scoped(HomePageController::class, function ($c) {
    return new HomePageController(
        $c->get(CiudadService::class),
        $c->get(NovedadService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(NovedadRepository::class, function ($c) {
    return new NovedadRepository($c->get(Database::class));
});

$container->scoped(NovedadService::class, function ($c) {
    return new NovedadService($c->get(NovedadRepository::class));
});

$container->scoped(PromocionRepository::class, function ($c) {
    return new PromocionRepository($c->get(Database::class));
});

$container->scoped(PromocionService::class, function ($c) {
    return new PromocionService(
        $c->get(PromocionRepository::class),
        $c->get(AerolineaService::class)
    );
});

$container->scoped(PanelUsuarioRepository::class, function ($c) {
    return new PanelUsuarioRepository($c->get(Database::class));
});

$container->scoped(PanelUsuarioService::class, function ($c) {
    return new PanelUsuarioService($c->get(PanelUsuarioRepository::class));
});

$container->scoped(AdminDashboardPageController::class, function ($c) {
    return new AdminDashboardPageController(
        $c->get(PromocionService::class),
        $c->get(PanelUsuarioService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(ListadoCeosPageController::class, function ($c) {
    return new ListadoCeosPageController(
        $c->get(PanelUsuarioService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(CrearCeoPageController::class, function ($c) {
    return new CrearCeoPageController(
        $c->get(AerolineaService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(CrearCeoActionController::class, function ($c) {
    return new CrearCeoActionController(
        $c->get(PanelUsuarioService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(BorrarPromocionActionController::class, function ($c) {
    return new BorrarPromocionActionController(
        $c->get(PromocionService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(CrearPromocionPageController::class, function ($c) {
    return new CrearPromocionPageController($c->get(ViewResponse::class));
});

$container->scoped(CrearPromocionActionController::class, function ($c) {
    return new CrearPromocionActionController(
        $c->get(PromocionService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(DesactivarPromocionActionController::class, function ($c) {
    return new DesactivarPromocionActionController(
        $c->get(PromocionService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(EditarPromocionPageController::class, function ($c) {
    return new EditarPromocionPageController(
        $c->get(PromocionService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(EditarPromocionActionController::class, function ($c) {
    return new EditarPromocionActionController(
        $c->get(PromocionService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(ListadoPromocionesPageController::class, function ($c) {
    return new ListadoPromocionesPageController(
        $c->get(PromocionService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(SolicitarActivacionPageController::class, function ($c) {
    return new SolicitarActivacionPageController($c->get(ViewResponse::class));
});

$container->scoped(SolicitudesPromocionesPageController::class, function ($c) {
    return new SolicitudesPromocionesPageController(
        $c->get(PromocionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(AprobarPromocionActionController::class, function ($c) {
    return new AprobarPromocionActionController(
        $c->get(PromocionService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(DenegarPromocionActionController::class, function ($c) {
    return new DenegarPromocionActionController(
        $c->get(PromocionService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(SolicitarActivacionActionController::class, function ($c) {
    return new SolicitarActivacionActionController(
        $c->get(PromocionService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(NovedadesPageController::class, function ($c) {
    return new NovedadesPageController(
        $c->get(NovedadService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(AdminNovedadesPageController::class, function ($c) {
    return new AdminNovedadesPageController(
        $c->get(NovedadService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(CrearNovedadPageController::class, function ($c) {
    return new CrearNovedadPageController($c->get(ViewResponse::class));
});

$container->scoped(EditarNovedadPageController::class, function ($c) {
    return new EditarNovedadPageController(
        $c->get(NovedadService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(CrearNovedadActionController::class, function ($c) {
    return new CrearNovedadActionController(
        $c->get(NovedadService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(EditarNovedadActionController::class, function ($c) {
    return new EditarNovedadActionController(
        $c->get(NovedadService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(BorrarNovedadActionController::class, function ($c) {
    return new BorrarNovedadActionController(
        $c->get(NovedadService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(MiPerfilPageController::class, function ($c) {
    return new MiPerfilPageController($c->get(ViewResponse::class));
});

$container->scoped(ReservaRepository::class, function ($c) {
    return new ReservaRepository($c->get(Database::class));
});

$container->scoped(ReservaService::class, function ($c) {
    return new ReservaService($c->get(ReservaRepository::class));
});

$container->scoped(CancelarReservaActionController::class, function ($c) {
    return new CancelarReservaActionController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(MisReservasPageController::class, function ($c) {
    return new MisReservasPageController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(ReservaDetallePageController::class, function ($c) {
    return new ReservaDetallePageController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(CrearReservaActionController::class, function ($c) {
    return new CrearReservaActionController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(ConfirmacionPageController::class, function ($c) {
    return new ConfirmacionPageController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(PasajerosPageController::class, function ($c) {
    return new PasajerosPageController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(GuardarPasajerosActionController::class, function ($c) {
    return new GuardarPasajerosActionController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(PagoPageController::class, function ($c) {
    return new PagoPageController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(VueloRepository::class, function ($c) {
    return new VueloRepository($c->get(Database::class));
});

$container->scoped(VueloService::class, function ($c) {
    return new VueloService(
        $c->get(VueloRepository::class),
        $c->get(CiudadService::class),
        $c->get(AerolineaService::class)
    );
});

$container->scoped(VueloController::class, function ($c) {
    return new VueloController($c->get(VueloService::class));
});

$container->scoped(BuscarVuelosPageController::class, function ($c) {
    return new BuscarVuelosPageController(
        $c->get(CiudadService::class),
        $c->get(VueloService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(BorrarVueloActionController::class, function ($c) {
    return new BorrarVueloActionController(
        $c->get(VueloService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(ListadoVuelosPageController::class, function ($c) {
    return new ListadoVuelosPageController(
        $c->get(VueloService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(CrearVueloPageController::class, function ($c) {
    return new CrearVueloPageController(
        $c->get(CiudadService::class),
        $c->get(VueloService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(CrearVueloActionController::class, function ($c) {
    return new CrearVueloActionController(
        $c->get(VueloService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(EditarVueloPageController::class, function ($c) {
    return new EditarVueloPageController(
        $c->get(CiudadService::class),
        $c->get(VueloService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(EditarVueloActionController::class, function ($c) {
    return new EditarVueloActionController(
        $c->get(VueloService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(CeoDashboardPageController::class, function ($c) {
    return new CeoDashboardPageController(
        $c->get(VueloService::class),
        $c->get(PromocionService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(ListadoReportesPageController::class, function ($c) {
    return new ListadoReportesPageController(
        $c->get(ViewResponse::class)
    );
});

$container->scoped(AdminListadoReportesPageController::class, function ($c) {
    return new AdminListadoReportesPageController(
        $c->get(ViewResponse::class)
    );
});

$container->scoped(AdminReporteVentasPageController::class, function ($c) {
    return new AdminReporteVentasPageController(
        $c->get(ReporteService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(AdminReporteCeosPageController::class, function ($c) {
    return new AdminReporteCeosPageController(
        $c->get(ReporteService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(AdminReporteVuelosPageController::class, function ($c) {
    return new AdminReporteVuelosPageController(
        $c->get(ReporteService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(ReporteRepository::class, function ($c) {
    return new ReporteRepository($c->get(Database::class));
});

$container->scoped(ReporteService::class, function ($c) {
    return new ReporteService($c->get(ReporteRepository::class));
});

$container->scoped(ReporteVentasPageController::class, function ($c) {
    return new ReporteVentasPageController(
        $c->get(ReporteService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(ReporteOcupacionPageController::class, function ($c) {
    return new ReporteOcupacionPageController(
        $c->get(ReporteService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

function resolvePage(
    array $route,
    Container $container,
    array $query,
    string $layoutPath
): void {
    if (isset($route['controller'], $route['action'])) {
        $controller = $container->get($route['controller']);
        $controller->{$route['action']}([], $query, $layoutPath);
        return;
    }

    // Cuando todas las paginas que usan datos tengan el PageController
    // esto se puede sacar
    $viewData = isset($route['data']) ? $route['data']() : [];

    $container->get(ViewResponse::class)->render(
        $route['view'],
        $route['title'],
        $viewData,
        200,
        $layoutPath
    );
}

function routePublicPage(
    array $routes,
    string $requestPath,
    array $query,
    Container $container
): bool {
    if (!isset($routes[$requestPath])) {
        return false;
    }

    resolvePage(
        $routes[$requestPath],
        $container,
        $query,
        __DIR__ . '/../app/shared/views/layouts/public.layout.php'
    );

    return true;
}

function routeProtectedPage(
    array $routes,
    string $requestPath,
    array $query,
    Container $container,
    string $middlewareClass,
    string $layoutPath,
    string $forbiddenMessage
): bool {
    if (!isset($routes[$requestPath])) {
        return false;
    }

    try {
        $container->get($middlewareClass)->handle();
    } catch (HttpException $exception) {
        Flash::error($forbiddenMessage);
        RedirectResponse::to($exception->getStatusCode() === 401 ? '/auth/login' : '/', [], 303);
        return true;
    }

    resolvePage($routes[$requestPath], $container, $query, $layoutPath);

    return true;
}

function routeAdminPage(
    array $routes,
    string $requestPath,
    array $query,
    Container $container
): bool {
    return routeProtectedPage(
        $routes,
        $requestPath,
        $query,
        $container,
        AdminMiddleware::class,
        __DIR__ . '/../app/shared/views/layouts/admin.layout.php',
        'Necesitas permisos de administrador para acceder a esta pagina.'
    );
}

function routeProtectedPublicPage(
    array $routes,
    string $requestPath,
    array $query,
    Container $container
): bool {
    return routeProtectedPage(
        $routes,
        $requestPath,
        $query,
        $container,
        AuthMiddleware::class,
        __DIR__ . '/../app/shared/views/layouts/public.layout.php',
        'Necesitas iniciar sesion para acceder a esta pagina.'
    );
}

function routeCeoPage(
    array $routes,
    string $requestPath,
    array $query,
    Container $container
): bool {
    return routeProtectedPage(
        $routes,
        $requestPath,
        $query,
        $container,
        CeoMiddleware::class,
        __DIR__ . '/../app/shared/views/layouts/ceo.layout.php',
        'Necesitas permisos de CEO para acceder a esta pagina.'
    );
}

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = $scriptDir === '/' ? '' : rtrim($scriptDir, '/');

if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
    $requestPath = substr($requestPath, strlen($basePath)) ?: '/';
}

$requestPath = $requestPath !== '/' ? rtrim($requestPath, '/') : '/';
$requestQuery = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_QUERY);
parse_str(is_string($requestQuery) ? $requestQuery : '', $queryParams);

$publicRoutes = [
    '/' => [
        'controller' => HomePageController::class,
        'action' => 'show',
    ],
    '/auth/login' => [
        'controller' => LoginPageController::class,
        'action' => 'show',
    ],
    '/auth/registro' => [
        'controller' => RegistroPageController::class,
        'action' => 'show',
    ],
    '/auth/registro/confirmacion-enviada' => [
        'view' => __DIR__ . '/../app/auth/views/pages/registro-confirmacion-enviada.page.php',
        'title' => 'Confirmacion enviada - Flyto',
    ],
    '/auth/cuenta-confirmada' => [
        'view' => __DIR__ . '/../app/auth/views/pages/cuenta-confirmada.page.php',
        'title' => 'Cuenta confirmada - Flyto',
    ],
    '/auth/recuperar-contrasena' => [
        'controller' => RecuperarContrasenaPageController::class,
        'action' => 'show',
    ],
    '/auth/recuperar-contrasena/codigo' => [
        'controller' => RecuperarContrasenaTokenPageController::class,
        'action' => 'show',
    ],
    '/auth/recuperar-contrasena/cambiar' => [
        'view' => __DIR__ . '/../app/auth/views/pages/recuperar-contrasena-cambiar.page.php',
        'title' => 'Cambiar contrasena - Flyto',
    ],
    '/novedades' => [
        'controller' => NovedadesPageController::class,
        'action' => 'show',
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
        'controller' => BuscarVuelosPageController::class,
        'action' => 'show',
    ],
];

$protectedPublicRoutes = [
    '/reservas/pasajeros' => [
        'controller' => PasajerosPageController::class,
        'action' => 'show',
    ],
    '/reservas/pago' => [
        'controller' => PagoPageController::class,
        'action' => 'show',
    ],
    '/reservas/confirmacion' => [
        'controller' => ConfirmacionPageController::class,
        'action' => 'show',
    ],
    '/mi-perfil/mis-reservas' => [
        'controller' => MisReservasPageController::class,
        'action' => 'show',
    ],
    '/mi-perfil/datos' => [
        'controller' => MiPerfilPageController::class,
        'action' => 'show',
    ],
    '/reservas/detalle' => [
        'controller' => ReservaDetallePageController::class,
        'action' => 'show',
    ],
];

$adminRoutes = [
    '/admin' => [
        'controller' => AdminDashboardPageController::class,
        'action' => 'show',
    ],
    '/admin/aerolineas' => [
        'controller' => ListadoAerolineasPageController::class,
        'action' => 'show',
    ],
    '/admin/aerolineas/crear' => [
        'controller' => CrearAerolineaPageController::class,
        'action' => 'show',
    ],
    '/admin/aerolineas/editar' => [
        'controller' => EditarAerolineaPageController::class,
        'action' => 'show',
    ],
    '/admin/ceos' => [
        'controller' => ListadoCeosPageController::class,
        'action' => 'show',
    ],
    '/admin/ceos/crear' => [
        'controller' => CrearCeoPageController::class,
        'action' => 'show',
    ],
    '/admin/promociones' => [
        'controller' => SolicitudesPromocionesPageController::class,
        'action' => 'show',
    ],
    '/admin/reportes' => [
        'controller' => AdminListadoReportesPageController::class,
        'action' => 'show',
    ],
    '/admin/reportes/ventas' => [
        'controller' => AdminReporteVentasPageController::class,
        'action' => 'show',
    ],
    '/admin/reportes/ceos' => [
        'controller' => AdminReporteCeosPageController::class,
        'action' => 'show',
    ],
    '/admin/reportes/vuelos' => [
        'controller' => AdminReporteVuelosPageController::class,
        'action' => 'show',
    ],
    '/admin/novedades' => [
        'controller' => AdminNovedadesPageController::class,
        'action' => 'show',
    ],
    '/admin/novedades/crear' => [
        'controller' => CrearNovedadPageController::class,
        'action' => 'show',
    ],
    '/admin/novedades/editar' => [
        'controller' => EditarNovedadPageController::class,
        'action' => 'show',
    ],
];

$ceoRoutes = [
    '/ceo' => [
        'controller' => CeoDashboardPageController::class,
        'action' => 'show',
    ],
    '/ceo/vuelos' => [
        'controller' => ListadoVuelosPageController::class,
        'action' => 'show',
    ],
    '/ceo/promociones' => [
        'controller' => ListadoPromocionesPageController::class,
        'action' => 'show',
    ],
    '/ceo/reportes' => [
        'controller' => ListadoReportesPageController::class,
        'action' => 'show',
    ],
    '/ceo/reportes/ventas' => [
        'controller' => ReporteVentasPageController::class,
        'action' => 'show',
    ],
    '/ceo/reportes/vuelos' => [
        'controller' => ReporteOcupacionPageController::class,
        'action' => 'show',
    ],
    '/ceo/promociones/crear' => [
        'controller' => CrearPromocionPageController::class,
        'action' => 'show',
    ],
    '/ceo/promociones/editar' => [
        'controller' => EditarPromocionPageController::class,
        'action' => 'show',
    ],
    '/ceo/promociones/solicitar-activacion' => [
        'controller' => SolicitarActivacionPageController::class,
        'action' => 'show',
    ],
    '/ceo/vuelos/crear' => [
        'controller' => CrearVueloPageController::class,
        'action' => 'show',
    ],
    '/ceo/vuelos/editar' => [
        'controller' => EditarVueloPageController::class,
        'action' => 'show',
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (routeAdminPage($adminRoutes, $requestPath, $queryParams, $container)) {
        return;
    }

    if (routeCeoPage($ceoRoutes, $requestPath, $queryParams, $container)) {
        return;
    }

    if (routeProtectedPublicPage($protectedPublicRoutes, $requestPath, $queryParams, $container)) {
        return;
    }

    if (routePublicPage($publicRoutes, $requestPath, $queryParams, $container)) {
        return;
    }
}

$router = new Router();

$router->registerModule(require __DIR__ . '/../app/auth/routes.php');
$router->registerModule(require __DIR__ . '/../app/aerolineas/routes.php');
$router->registerModule(require __DIR__ . '/../app/contacto/routes.php');
$router->registerModule(require __DIR__ . '/../app/novedades/routes.php');
$router->registerModule(require __DIR__ . '/../app/promociones/routes.php');
$router->registerModule(require __DIR__ . '/../app/promociones/admin-routes.php');
$router->registerModule(require __DIR__ . '/../app/reservas/routes.php');
$router->registerModule(require __DIR__ . '/../app/usuarios/routes.php');
$router->registerModule(require __DIR__ . '/../app/vuelos/routes.php');
$router->registerModule(require __DIR__ . '/../app/vuelos/crear-routes.php');
$router->registerModule(require __DIR__ . '/../app/vuelos/editar-routes.php');
$router->registerModule(require __DIR__ . '/../app/vuelos/borrar-routes.php');

$normalizedUri = $requestPath;

if (is_string($requestQuery) && $requestQuery !== '') {
    $normalizedUri .= '?' . $requestQuery;
}

$router->resolve(
    $_SERVER['REQUEST_METHOD'],
    $normalizedUri,
    $container
);
