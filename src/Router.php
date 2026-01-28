<?php declare(strict_types=1);

namespace App;

class Router
{
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        if ($uri === '/' && $method === 'GET') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'ok', 'message' => 'API running']);
            return;
        }

        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
}
