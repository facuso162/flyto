<?php

namespace App\Novedades\Controllers;

use App\Novedades\Services\NovedadService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;
use Throwable;

require_once __DIR__ . '/../services/novedad.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class EditarNovedadPageController
{
    public function __construct(
        private NovedadService $novedadService,
        private ViewResponse $viewResponse
    ) {
    }

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        try {
            $novedad = $this->novedadService->getById($this->novedadId($query));
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
            RedirectResponse::to('/admin/novedades', [], 303);
            return;
        } catch (Throwable) {
            Flash::error('No pudimos cargar la novedad para editar.');
            RedirectResponse::to('/admin/novedades', [], 303);
            return;
        }

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/editar-novedad.page.php',
            'Editar novedad - Panel Admin - Flyto',
            [
                'novedad' => $novedad,
                'flash' => Flash::consume(),
                'oldInput' => Flash::consumeOld(),
            ],
            200,
            $layoutPath
        );
    }

    private function novedadId(array $query): int
    {
        $value = $query['id'] ?? null;
        $id = is_scalar($value)
            ? filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;

        if ($id === false) {
            throw new HttpException('La novedad solicitada no es valida.', 400);
        }

        return (int) $id;
    }
}
