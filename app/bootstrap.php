<?php

declare(strict_types=1);

$root = dirname(__DIR__);

if (is_file($root . '/vendor/autoload.php')) {
    require $root . '/vendor/autoload.php';
} else {
    spl_autoload_register(static function (string $class) use ($root): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $file = $root . '/app/' . str_replace('\\', '/', $relative) . '.php';
        if (is_file($file)) {
            require $file;
        }
    });
}

$dotenv = Dotenv\Dotenv::createImmutable($root);
$dotenv->safeLoad();

require_once $root . '/app/Support/helpers.php';

$config = require $root . '/config/app.php';
$config['mail'] = require $root . '/config/mail.php';
$config['security'] = require $root . '/config/security.php';

date_default_timezone_set($config['timezone']);

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_name($config['session']['name']);

    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params([
        'httponly' => true,
        'secure' => $isHttps,
        'samesite' => 'Lax',
        'path' => '/',
    ]);

    session_start();
}

return [
    'root' => $root,
    'config' => $config,
];
