<?php

declare(strict_types=1);

/**
 * Einfacher RegExp-Router mit Unterstützung für Locale-Präfixe.
 */
final class Router
{
    /** @var array<int, array{pattern:string, handler:callable|array, methods:array}> */
    private array $routes = [];

    public function get(string $pattern, callable|array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable|array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    private function add(string $method, string $pattern, callable|array $handler): void
    {
        $this->routes[] = [
            'pattern' => '#^' . $pattern . '$#u',
            'handler' => $handler,
            'methods' => [$method],
        ];
    }

    public function dispatch(string $method, string $path): void
    {
        $path = '/' . trim(rawurldecode($path), '/');
        if ($path === '/index.php') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if (!in_array($method, $route['methods'], true)) {
                continue;
            }
            $params = [];
            if (preg_match($route['pattern'], $path, $params) === 1) {
                $params = array_filter($params, 'is_string', ARRAY_FILTER_USE_KEY);
                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$class, $action] = $handler;
                    (new $class())->$action($params);
                } else {
                    $handler($params);
                }
                return;
            }
        }

        if ($method === 'POST') {
            Response::abort(404);
        }
        Response::notFound();
    }
}
