<?php

declare(strict_types=1);

use App\Router;

function registerRoutes(Router $router): void
{
    $router->get('/', fn (): array => ['status' => 'ok', 'message' => 'API running']);

    $usersControllerClass = 'App\\Controllers\\UsersController';
    $usersController = new $usersControllerClass();
    $router->get('/api/users', [$usersController, 'index']);
    $router->post('/api/users', [$usersController, 'store']);
    $router->get('/api/users/{id}', [$usersController, 'show']);
    $router->put('/api/users/{id}', [$usersController, 'update']);
    $router->delete('/api/users/{id}', [$usersController, 'destroy']);
}
