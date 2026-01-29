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

require __DIR__ . '/src/routes.php';
registerRoutes($router);

return $router;
