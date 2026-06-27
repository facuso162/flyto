<?php

namespace App\Aerolineas\Controllers;

use App\Aerolineas\Services\AerolineaService;
use App\Paises\Services\PaisService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;
use Throwable;

require_once __DIR__ . '/../../paises/services/pais.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/aerolinea.service.php';

class EditarAerolineaPageController
{
    public function __construct(
        private AerolineaService $aerolineaService,
        private PaisService $paisService,
        private ViewResponse $viewResponse
    ) {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        try {
            $aerolinea = $this->aerolineaService->getPorId($this->aerolineaId($_GET));
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
            RedirectResponse::to('/admin/aerolineas', [], 303);
            return;
        } catch (Throwable) {
            Flash::error('No pudimos cargar la aerolinea para editar.');
            RedirectResponse::to('/admin/aerolineas', [], 303);
            return;
        }

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/editar-aerolinea.page.php',
            'Editar aerolinea - Panel Admin - Flyto',
            [
                'aerolinea' => $aerolinea,
                'paises' => $this->paisService->getAll(),
                'flash' => Flash::consume(),
                'oldInput' => Flash::consumeOld(),
            ],
            200,
            $layoutPath
        );
    }

    private function aerolineaId(array $query): int
    {
        $value = $query['id'] ?? null;
        $id = is_scalar($value)
            ? filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;

        if ($id === false) {
            throw new HttpException('La aerolinea solicitada no es valida.', 400);
        }

        return (int) $id;
    }
}
