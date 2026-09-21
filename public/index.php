<?php

declare(strict_types=1);

$app = require dirname(__DIR__) . '/app/bootstrap.php';
$routes = require dirname(__DIR__) . '/routes/web.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

$handler = $routes[$method][$path] ?? null;

if ($handler === null) {
    abort404();
}

if (is_string($handler) && class_exists($handler)) {
    (new $handler())($app);
    exit;
}

$handler($app);
