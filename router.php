<?php

declare(strict_types=1);

$public = realpath(__DIR__ . '/public');
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');

// The built-in PHP server does not apply public/.htaccess.
if (preg_match('~^/admin(?:/|$)~', $path)) {
    require $public . '/admin/index.php';
    return;
}

$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$isStatic = match (true) {
    $path === '/robots.txt', $path === '/sitemap.xml' => true,
    str_starts_with($path, '/assets/') => in_array(
        $extension,
        ['css', 'js', 'jpeg', 'jpg', 'png', 'webp', 'gif', 'ico', 'svg', 'woff', 'woff2'],
        true
    ),
    str_starts_with($path, '/uploads/') => in_array($extension, ['jpeg', 'jpg', 'png', 'webp', 'gif'], true),
    default => false,
};

if (
    $isStatic
    && !str_contains($path, "\0")
    && !str_contains($path, '\\')
    && !preg_match('~/(?:\.|/)|[. ](?:/|$)|:~', $path)
) {
    $file = realpath($public . $path);
    if (
        $file !== false
        && str_starts_with($file, $public . DIRECTORY_SEPARATOR)
        && is_file($file)
        && strtolower(pathinfo($file, PATHINFO_EXTENSION)) === $extension
    ) {
        return false;
    }
}

require $public . '/index.php';
