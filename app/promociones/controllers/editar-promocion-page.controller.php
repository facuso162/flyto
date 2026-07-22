<?php

namespace App\Promociones\Controllers;

use App\Auth\Services\SessionService;
use App\Promociones\Services\PromocionService;
use App\Promociones\Validators\PromocionValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/promocion.service.php';
require_once __DIR__ . '/../validators/promocion.validator.php';

class EditarPromocionPageController
{
    public function __construct(
        private PromocionService $promocionService,
        private SessionService $sessionService,
        private ViewResponse $viewResponse
    ) {
    }

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        try {
            PromocionValidator::edicionId($_GET);
            // TODO - No poner un fallback si no encuentra el usuario, lanzar un error
            $usuario = $this->sessionService->getUser() ?? [];
            $promocion = $this->promocionService->getEditableByCeoId(
                (int) $query['id'],
                // TODO - No poner un fallback si no encuentra el id, lanzar un error
                (int) ($usuario['id'] ?? 0)
            );
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
            RedirectResponse::to('/ceo/promociones', [], 303);
            return;
        }

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/editar-promocion.page.php',
            'Editar promoción - Panel CEO - Flyto',
            [
                'promocion' => $promocion,
                'flash' => Flash::consume(),
                'oldInput' => Flash::consumeOld(),
            ],
            200,
            $layoutPath
        );
    }
}
