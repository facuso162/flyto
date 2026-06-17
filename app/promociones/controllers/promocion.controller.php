<?php

namespace App\Promociones\Controllers;

use App\Auth\Middlewares\AdminMiddleware;
use App\Auth\Middlewares\CeoMiddleware;
use App\Auth\Services\SessionService;
use App\Promociones\Services\PromocionService;
use App\Promociones\Validators\PromocionValidator;
use App\Shared\Http\JsonResponse;
use Throwable;

require_once __DIR__ . '/../../auth/middlewares/admin.middleware.php';
require_once __DIR__ . '/../../auth/middlewares/ceo.middleware.php';
require_once __DIR__ . '/../services/promocion.service.php';
require_once __DIR__ . '/../validators/promocion.validator.php';
require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/json-response.php';

class PromocionController
{
    private PromocionService $promocionService;
    private SessionService $sessionService;

    public function __construct(
        PromocionService $promocionService,
        SessionService $sessionService
    ) {
        $this->promocionService = $promocionService;
        $this->sessionService = $sessionService;
    }

    public function crear(array $params = [], array $query = []): void
    {
        try {
            $this->ceoOnly();
            
            // Obtenemos el ID de la aerolínea desde la sesión del CEO
            $aerolineaId = $this->sessionService->get('aerolinea_id'); 

            $dto = PromocionValidator::crear($_POST);
            $promocion = $this->promocionService->crear($dto, $aerolineaId);

            JsonResponse::success([
                'message' => 'Promoción creada y enviada para aprobación.',
                'promocion' => $promocion->toArray(),
            ], 201);
        } catch (Throwable $exception) {
            JsonResponse::exception($exception);
        }
    }

    public function aprobar(array $params = [], array $query = []): void
    {
        try {
            $this->adminOnly();

            $id = PromocionValidator::aprobar($_POST);
            $promocion = $this->promocionService->aprobar($id);

            JsonResponse::success([
                'message' => 'Promoción aprobada correctamente.',
                'promocion' => $promocion->toArray(),
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

    private function ceoOnly(): void
    {
        // Asumiendo que tienes un CeoMiddleware creado de manera similar al AdminMiddleware
        $middleware = new CeoMiddleware($this->sessionService);
        $middleware->handle();
    }
}