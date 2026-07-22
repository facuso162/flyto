<?php

namespace App\Promociones\Controllers;

use App\Promociones\Validators\PromocionValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../validators/promocion.validator.php';

class SolicitarActivacionPageController
{
    public function __construct(private ViewResponse $viewResponse)
    {
    }

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        try {
            PromocionValidator::activacionId($_GET);
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
            RedirectResponse::to('/ceo/promociones', [], 303);
            return;
        }

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/solicitar-activacion.page.php',
            'Solicitar activación - Panel CEO - Flyto',
            // TODO - Si no encuentra el id, lanzar error, procesarlo antes de ejecutar el render
            ['promocionId' => (int) $_GET['id']],
            200,
            $layoutPath
        );
    }
}
