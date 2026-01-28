<?php

declare(strict_types=1);

try {
    $router = require dirname(__DIR__) . '/bootstrap.php';
    $router->dispatch();
} catch (Throwable $e) {
    \header('Content-Type: application/json');
    \http_response_code(500);
    echo \json_encode(['error' => 'Internal Server Error']);
}
