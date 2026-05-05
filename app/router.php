<?php

namespace App;

use App\Container;

require_once __DIR__ . '/container.php';

class Router
{
    private array $routes = [];

    public function registerModule(array $module): void {
        $prefix = $module['prefix'] ?? '';
        foreach ($module['routes'] as $r) {
            [$method, $path, $controller, $action] = $r;

            $this->routes[] = [
                'method' => $method,
                'path' => $prefix . $path,
                'controller' => $controller,
                'action' => $action
            ];
        }
    }

    public function resolve(string $method, string $uri, Container $container): void {
        $path = parse_url($uri, PHP_URL_PATH);

        $queryString = parse_url($uri, PHP_URL_QUERY);
        parse_str($queryString, $queryParams);

        // echo 'Ruta solicitada: ' . '<b>' . $path . ' con método ' . $method . '</b>' .'<br><br>';

        foreach ($this->routes as $route) {

            // echo 'Comparando con ruta: ' . '<b>' . $route['path'] . ' y método ' . $route['method'] . '</b>' . '<br><br>';

            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = preg_replace('#\{([\w]+)\}#', '(?P<$1>[^/]+)', $route['path']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $path, $matches)) {

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                $controller = $container->get($route['controller']);

                $controller->{$route['action']}($params, $queryParams);

                return;
            }
        }

        // TODO: Es esta manera de manejar el 404 la mejor?
        // Fallback 404
        echo 'Mensaje de error del router ante ruta no encontrada <br><br>';
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
    }
}
