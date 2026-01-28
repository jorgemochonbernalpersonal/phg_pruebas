<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

if (class_exists('Dotenv\Dotenv')) {
    $dotenvClass = 'Dotenv\Dotenv';
    $dotenv = $dotenvClass::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

$routerClass = 'App\\Router';
$router = new $routerClass();

$router->get('/', fn (): array => ['status' => 'ok', 'message' => 'API running']);

return $router;
