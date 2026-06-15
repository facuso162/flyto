<?php

namespace App\Novedades\Controllers;

use App\Auth\Middlewares\AdminMiddleware;
use App\Auth\Services\SessionService;
use App\Novedades\Services\NovedadService;
use App\Novedades\Validators\NovedadValidator;
use App\Shared\Http\JsonResponse;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/admin.middleware.php';
require_once __DIR__ . '/../services/novedad.service.php';
require_once __DIR__ . '/../validators/novedad.validator.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/json-response.php';

class NovedadController
{
    private NovedadService $novedadService;
    private SessionService $sessionService;

    public function __construct(
        NovedadService $novedadService,
        SessionService $sessionService
    ) {
        $this->novedadService = $novedadService;
        $this->sessionService = $sessionService;
    }

    public function getUltimas(array $params = [], array $query = []): void
    {
        try {
            $novedades = $this->novedadService->getUltimas();

            JsonResponse::success([
                'novedades' => array_map(fn ($novedad) => $novedad->toArray(), $novedades),
            ]);
        } catch (Throwable $exception) {
            JsonResponse::exception($exception);
        }
    }

    public function getVigentes(array $params = [], array $query = []): void
    {
        try {
            $novedades = $this->novedadService->getVigentes();

            JsonResponse::success([
                'novedades' => array_map(fn ($novedad) => $novedad->toArray(), $novedades),
            ]);
        } catch (Throwable $exception) {
            JsonResponse::exception($exception);
        }
    }

    public function getTodas(array $params = [], array $query = []): void
    {
        try {
            $this->adminOnly();
            $novedades = $this->novedadService->getTodas();

            JsonResponse::success([
                'novedades' => array_map(fn ($novedad) => $novedad->toArray(), $novedades),
            ]);
        } catch (Throwable $exception) {
            JsonResponse::exception($exception);
        }
    }

    public function crear(array $params = [], array $query = []): void
    {
        try {
            $this->adminOnly();

            $dto = NovedadValidator::crear($_POST);
            $novedad = $this->novedadService->crear($dto);

            JsonResponse::success([
                'message' => 'Novedad creada correctamente.',
                'novedad' => $novedad->toArray(),
            ], 201);
        } catch (Throwable $exception) {
            JsonResponse::exception($exception);
        }
    }

    public function editar(array $params = [], array $query = []): void
    {
        try {
            $this->adminOnly();

            $dto = NovedadValidator::editar($_POST);
            $novedad = $this->novedadService->editar($dto);

            JsonResponse::success([
                'message' => 'Novedad actualizada correctamente.',
                'novedad' => $novedad->toArray(),
            ]);
        } catch (Throwable $exception) {
            JsonResponse::exception($exception);
        }
    }

    public function borrar(array $params = [], array $query = []): void
    {
        try {
            $this->adminOnly();

            $id = NovedadValidator::borrar($_POST);
            $this->novedadService->borrar($id);

            JsonResponse::success([
                'message' => 'Novedad eliminada correctamente.',
            ]);
        } catch (Throwable $exception) {
            JsonResponse::exception($exception);
        }
    }

    private function adminOnly(): void
    {
        $middleware = new AdminMiddleware($this->sessionService);
        $middleware->handle();
    }
}
