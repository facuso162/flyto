<?php

namespace App\Reservas\Controllers;

use App\Auth\Services\SessionService;
use App\Reservas\Dtos\CrearReservaDto;
use App\Reservas\Models\MetodoPago;
use App\Reservas\Models\Pasajero;
use App\Reservas\Services\ReservaService;
use App\Reservas\Validators\ReservaValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../dtos/crear-reserva.dto.php';
require_once __DIR__ . '/../services/reserva.service.php';
require_once __DIR__ . '/../validators/reserva.validator.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class CrearReservaActionController
{
    private ReservaService $reservaService;
    private SessionService $sessionService;

    public function __construct(ReservaService $reservaService, SessionService $sessionService)
    {
        $this->reservaService = $reservaService;
        $this->sessionService = $sessionService;
    }

    public function crear(array $params = [], array $query = []): void
    {
        $this->sessionService->start();
        $usuario = $this->sessionService->getUser();

        if (!is_array($usuario) || !isset($usuario['id'])) {
            Flash::error('Necesitas iniciar sesion para realizar una reserva.');
            RedirectResponse::to('/auth/login', [], 303);
            return;
        }

        try {
            $data = $_POST;
            ReservaValidator::validate($data);

            $pasajeros = array_map(
                fn (array $pasajero) => new Pasajero(
                    id: null,
                    nombre: trim((string) $pasajero['nombre']),
                    apellido: trim((string) $pasajero['apellido']),
                    documento: trim((string) $pasajero['documento']),
                    pasaporte: trim((string) $pasajero['pasaporte']),
                    fechaNacimiento: new \DateTime((string) $pasajero['fechaNacimiento']),
                    telefonoContacto: trim((string) $pasajero['telefonoContacto']),
                    correoElectronico: trim((string) $pasajero['correoElectronico'])
                ),
                $data['pasajeros']
            );

            $numeroTarjeta = (string) $data['pago']['numeroTarjeta'];
            $metodoPago = new MetodoPago(
                id: null,
                nombreTitular: trim((string) $data['pago']['nombreTitular']),
                ultimosCuatroDigitos: substr($numeroTarjeta, -4),
                vencimientoMes: (int) $data['pago']['vencimientoMes'],
                vencimientoAnio: (int) $data['pago']['vencimientoAnio'],
                fechaPago: new \DateTime()
            );

            $dto = new CrearReservaDto(
                usuarioId: (int) $usuario['id'],
                vueloId: (int) $data['vueloId'],
                pasajeros: $pasajeros,
                metodoPago: $metodoPago
            );

            $this->reservaService->crear($dto);
            Flash::success('La reserva se realizo correctamente.');
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
        } catch (Throwable) {
            Flash::error('No pudimos realizar la reserva. Intentalo nuevamente en unos minutos.');
        }

        RedirectResponse::to('/mi-perfil', [], 303);
    }
}
