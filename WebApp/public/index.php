<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use EventApp\Infrastructure\Router\Router;

$router = new Router();
$router->loadRoutes(__DIR__ . '/../src/Infrastructure/Router/routes.php');
$router->dispatch();
