<?php

declare(strict_types=1);

namespace App;

class Router
{
    private array $routes = [];

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            [$routeMethod, $routePath, $handler] = $route;
            if ($routeMethod === $method && $routePath === $uri) {
                $result = $handler();
                if (is_array($result)) {
                    $this->json($result, 200);
                }
                return;
            }
        }

        $this->json(['error' => 'Not Found'], 404);
    }

    private function json(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
    }

    public function get(string $path, callable $handler): void
    {
        $this->routes[] = ['GET', $path, $handler];
    }
    
    public function post(string $path, callable $handler): void
    {
        $this->routes[] = ['POST', $path, $handler];
    }
}
