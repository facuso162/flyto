<?php

namespace App\Aerolineas\Controllers;

use App\Aerolineas\Services\AerolineaService;
use App\Auth\Middlewares\AdminMiddleware;
use App\Auth\Services\SessionService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/admin.middleware.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../services/aerolinea.service.php';

class BorrarAerolineaActionController
{
    public function __construct(
        private AerolineaService $aerolineaService,
        private SessionService $sessionService
    ) {
    }

    public function borrar(array $params = [], array $query = []): void
    {
        try {
            $middleware = new AdminMiddleware($this->sessionService);
            $middleware->handle();
        } catch (HttpException $exception) {
            Flash::error('Necesitas permisos de administrador para borrar una aerolinea.');
            RedirectResponse::to($exception->getStatusCode() === 401 ? '/auth/login' : '/', [], 303);
            return;
        }

        $aerolineaId = $this->aerolineaId($_POST);
        if ($aerolineaId === null) {
            Flash::error('La aerolinea indicada no es valida.');
            RedirectResponse::to('/admin/aerolineas', [], 303);
            return;
        }

        try {
            $aerolinea = $this->aerolineaService->getPorId($aerolineaId);
            $this->aerolineaService->borrar($aerolinea);
            Flash::success('Aerolinea borrada correctamente.');
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
        } catch (Throwable) {
            Flash::error('No pudimos borrar la aerolinea. Intentalo nuevamente en unos minutos.');
        }

        RedirectResponse::to('/admin/aerolineas', [], 303);
    }

    private function aerolineaId(array $data): ?int
    {
        $valor = $data['aerolineaId'] ?? null;
        $id = is_scalar($valor)
            ? filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;

        return $id === false ? null : (int) $id;
    }
}
