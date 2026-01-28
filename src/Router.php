<?php

declare(strict_types=1);

namespace App;

class Router
{
    private array $routes = [];

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = \parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri = \rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            [$routeMethod, $routePath, $handler] = $route;
            if ($routeMethod === $method && $routePath === $uri) {
                $result = $handler();
                if (\is_array($result)) {
                    $this->json($result, 200);
                }
                return;
            }
        }

        $this->json(['error' => 'Not Found'], 404);
    }

    private function json(array $data, int $code = 200): void
    {
        \header('Content-Type: application/json');
        \http_response_code($code);
        echo \json_encode($data);
    }

    private function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [$method, $path, $handler];
    }

    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }
}
