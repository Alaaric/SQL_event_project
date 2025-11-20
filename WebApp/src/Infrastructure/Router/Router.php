<?php

namespace EventApp\Infrastructure\Router;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function loadRoutes(string $routeFile): void
    {
        $routeLoader = require $routeFile;
        $routeLoader($this);
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? 'list';

        if (!isset($this->routes[$method][$action])) {
            http_response_code(404);
            echo "Route non trouvée";
            return;
        }

        $result = $this->routes[$method][$action]();

        if (is_array($result)) {
            extract($result);
            require __DIR__ . '/../Views/index.php';
        }
    }
}
