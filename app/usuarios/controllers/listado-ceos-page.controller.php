<?php

namespace App\Usuarios\Controllers;

use App\Shared\Http\ViewResponse;
use App\Usuarios\Services\UsuarioService;

require_once __DIR__ . '/../../shared/http/view-response.php';
require_once __DIR__ . '/../services/usuario.service.php';

class ListadoCeosPageController
{
    private const TIPO_CEO = 'ceo';
    private const CEOS_POR_PAGINA = 3;

    public function __construct(
        private UsuarioService $usuarioService,
        private ViewResponse $viewResponse
    ) {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $ceos = $this->usuarioService->getByTipo(self::TIPO_CEO);
        $total = count($ceos);
        $totalPaginas = max(1, (int) ceil($total / self::CEOS_POR_PAGINA));
        $pagina = min($this->paginaDesdeQuery($query), $totalPaginas);
        $offset = ($pagina - 1) * self::CEOS_POR_PAGINA;

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/listado-ceos.page.php',
            'CEOs - Panel Admin - Flyto',
            [
                'ceos' => array_slice($ceos, $offset, self::CEOS_POR_PAGINA),
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginas,
                'totalCeos' => $total,
            ],
            200,
            $layoutPath
        );
    }

    private function paginaDesdeQuery(array $query): int
    {
        $valor = $query['pagina'] ?? 1;
        $pagina = is_scalar($valor) ? filter_var($valor, FILTER_VALIDATE_INT) : false;

        return $pagina !== false && $pagina > 0 ? $pagina : 1;
    }
}
