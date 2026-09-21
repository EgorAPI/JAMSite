<?php

declare(strict_types=1);

$app = require dirname(__DIR__, 2) . '/app/bootstrap.php';
$routes = require dirname(__DIR__, 2) . '/routes/admin.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/admin', PHP_URL_PATH) ?: '/admin';
$path = preg_replace('#^/admin#', '', $uriPath) ?: '/';
$path = rtrim($path, '/') ?: '/';

$handler = $routes[$method][$path] ?? null;

if ($handler === null) {
    http_response_code(404);
    echo 'Admin page not found.';
    exit;
}

$handler($app);
