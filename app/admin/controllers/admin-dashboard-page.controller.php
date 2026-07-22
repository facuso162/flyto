<?php

namespace App\Admin\Controllers;

use App\Promociones\Services\PromocionService;
use App\Shared\Http\ViewResponse;
use App\Usuarios\Services\UsuarioService;

class AdminDashboardPageController
{
    private const ESTADO_PROMOCION_PENDIENTE = 'pendiente_activacion'; // TODO - las constantes de tipos deberian estar en un enum
    private const TIPO_CEO = 'ceo'; // TODO - las constantes de tipos deberian estar en un enum
    private const LIMITE_DASHBOARD = 3;

    public function __construct(
        private PromocionService $promocionService,
        private UsuarioService $usuarioService,
        private ViewResponse $viewResponse
    ) {
    }

    // TODO - Dejar de usar el array $params y el array $query
    public function show(array $params, array $query, string $layoutPath): void
    {
        $promocionesPendientes = $this->promocionService->getByEstado(self::ESTADO_PROMOCION_PENDIENTE);
        $ceosRegistrados = $this->usuarioService->getConfirmadosByTipo(self::TIPO_CEO);

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/admin.page.php',
            'Panel Admin - Flyto',
            [
                'promocionesPendientes' => array_slice($promocionesPendientes, 0, self::LIMITE_DASHBOARD),
                'ceosRegistrados' => array_slice($ceosRegistrados, 0, self::LIMITE_DASHBOARD),
            ],
            200,
            $layoutPath
        );
    }
}
