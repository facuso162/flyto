<?php

namespace App\MapaSitio\Controllers;

use App\Auth\Services\SessionService;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class MapaSitioPageController
{
    public function __construct(
        private SessionService $sessionService,
        private ViewResponse $viewResponse
    ) {
    }

    public function showPublic(array $params, array $query, string $layoutPath): void
    {
        $this->sessionService->start();

        $categories = [
            [
                'title' => 'Explorar Flyto',
                'links' => [
                    ['label' => 'Inicio', 'href' => '/'],
                    ['label' => 'Buscar vuelos', 'href' => '/#hero-section'],
                    ['label' => 'Novedades', 'href' => '/novedades'],
                ],
            ],
            [
                'title' => 'Ayuda e información',
                'links' => [
                    ['label' => 'Preguntas frecuentes', 'href' => '/faq'],
                    ['label' => 'Contacto', 'href' => '/contacto'],
                ],
            ],
        ];

        if ($this->sessionService->isAuthenticated()) {
            $accountLinks = [
                ['label' => 'Mis reservas', 'href' => '/mi-perfil/mis-reservas'],
                ['label' => 'Mis datos', 'href' => '/mi-perfil/datos'],
            ];

            $user = $this->sessionService->getUser() ?? [];
            $role = strtolower((string) ($user['tipo_usuario']['nombre'] ?? ''));

            if ($role === 'ceo') {
                $accountLinks[] = ['label' => 'Panel CEO', 'href' => '/ceo'];
            } elseif ($role === 'administrador') {
                $accountLinks[] = ['label' => 'Panel Admin', 'href' => '/admin'];
            }

            $categories[] = [
                'title' => 'Tu cuenta',
                'links' => $accountLinks,
            ];
        } else {
            $categories[] = [
                'title' => 'Acceso',
                'links' => [
                    ['label' => 'Ingresar', 'href' => '/auth/login'],
                    ['label' => 'Registrarse', 'href' => '/auth/registro'],
                    ['label' => 'Recuperar contraseña', 'href' => '/auth/recuperar-contrasena'],
                ],
            ];
        }

        $this->render(
            'Mapa de Sitio',
            'Todos los destinos disponibles en el sitio público, organizados para encontrarlos rápidamente.',
            $categories,
            $layoutPath
        );
    }

    public function showCeo(array $params, array $query, string $layoutPath): void
    {
        $this->render(
            'Mapa de Sitio del CEO',
            'Accesos disponibles para administrar vuelos, promociones y reportes de tu aerolínea.',
            [
                [
                    'title' => 'Panel',
                    'links' => [
                        ['label' => 'Menú principal', 'href' => '/ceo'],
                    ],
                ],
                [
                    'title' => 'Vuelos',
                    'links' => [
                        ['label' => 'Listado de vuelos', 'href' => '/ceo/vuelos'],
                        ['label' => 'Crear vuelo', 'href' => '/ceo/vuelos/crear'],
                    ],
                ],
                [
                    'title' => 'Promociones',
                    'links' => [
                        ['label' => 'Listado de promociones', 'href' => '/ceo/promociones'],
                        ['label' => 'Crear promoción', 'href' => '/ceo/promociones/crear'],
                    ],
                ],
                [
                    'title' => 'Reportes',
                    'links' => [
                        ['label' => 'Todos los reportes', 'href' => '/ceo/reportes'],
                        ['label' => 'Reporte de ventas', 'href' => '/ceo/reportes/ventas'],
                        ['label' => 'Reporte de ocupación de vuelos', 'href' => '/ceo/reportes/vuelos'],
                    ],
                ],
            ],
            $layoutPath
        );
    }

    public function showAdmin(array $params, array $query, string $layoutPath): void
    {
        $this->render(
            'Mapa de Sitio del Admin',
            'Accesos disponibles para gestionar el contenido y la operación de Flyto.',
            [
                [
                    'title' => 'Panel',
                    'links' => [
                        ['label' => 'Menú principal', 'href' => '/admin'],
                    ],
                ],
                [
                    'title' => 'Aerolíneas',
                    'links' => [
                        ['label' => 'Listado de aerolíneas', 'href' => '/admin/aerolineas'],
                        ['label' => 'Crear aerolínea', 'href' => '/admin/aerolineas/crear'],
                    ],
                ],
                [
                    'title' => 'CEOs',
                    'links' => [
                        ['label' => 'Listado de CEOs', 'href' => '/admin/ceos'],
                        ['label' => 'Crear CEO', 'href' => '/admin/ceos/crear'],
                    ],
                ],
                [
                    'title' => 'Contenido y promociones',
                    'links' => [
                        ['label' => 'Novedades', 'href' => '/admin/novedades'],
                        ['label' => 'Crear novedad', 'href' => '/admin/novedades/crear'],
                        ['label' => 'Solicitudes de promociones', 'href' => '/admin/promociones'],
                    ],
                ],
                [
                    'title' => 'Reportes',
                    'links' => [
                        ['label' => 'Todos los reportes', 'href' => '/admin/reportes'],
                        ['label' => 'Reporte de ventas', 'href' => '/admin/reportes/ventas'],
                        ['label' => 'Reporte de CEOs', 'href' => '/admin/reportes/ceos'],
                        ['label' => 'Reporte de vuelos', 'href' => '/admin/reportes/vuelos'],
                    ],
                ],
            ],
            $layoutPath
        );
    }

    private function render(
        string $title,
        string $description,
        array $categories,
        string $layoutPath
    ): void {
        $this->viewResponse->render(
            __DIR__ . '/../views/pages/mapa-sitio.page.php',
            $title . ' - Flyto',
            [
                'mapTitle' => $title,
                'mapDescription' => $description,
                'categories' => $categories,
            ],
            200,
            $layoutPath
        );
    }
}
