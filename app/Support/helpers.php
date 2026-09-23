<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function render(string $template, array $data = [], string $layout = 'main'): void
{
    $root = dirname(__DIR__, 2);
    $templateFile = $root . '/templates/' . $template . '.php';
    $layoutFile = $root . '/templates/layouts/' . $layout . '.php';

    if (!is_file($templateFile) || !is_file($layoutFile)) {
        throw new RuntimeException('Template not found.');
    }

    extract($data, EXTR_SKIP);

    ob_start();
    require $templateFile;
    $content = ob_get_clean();

    require $layoutFile;
}

function abort404(): never
{
    http_response_code(404);

    render(
        'pages/404',
        [
            'title' => 'Страница не найдена',
            'robots' => 'noindex, nofollow',
        ]
    );

    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_validate(?string $token): bool
{
    if (
        empty($_SESSION['_csrf']) ||
        $token === null ||
        $token === ''
    ) {
        return false;
    }

    return hash_equals($_SESSION['_csrf'], $token);
}

function admin_is_authenticated(): bool
{
    return !empty($_SESSION['admin_authenticated']);
}

function admin_require_auth(array $app): void
{
    if (!admin_is_authenticated()) {
        header('Location: /admin/login');
        exit;
    }

    $timeout = (int) (
        $app['config']['security']['admin']['session_timeout']
        ?? 3600
    );

    $lastActivity = (int) ($_SESSION['admin_last_activity'] ?? 0);

    if ($lastActivity > 0 && (time() - $lastActivity) > $timeout) {
        unset(
            $_SESSION['admin_authenticated'],
            $_SESSION['admin_last_activity']
        );

        session_regenerate_id(true);

        header('Location: /admin/login');
        exit;
    }

    $_SESSION['admin_last_activity'] = time();
}
function admin_login_is_locked(array $app): bool
{
    $adminConfig = $app['config']['security']['admin'] ?? [];

    $maxAttempts = (int) ($adminConfig['max_login_attempts'] ?? 5);
    $lockSeconds = (int) ($adminConfig['login_lock_seconds'] ?? 300);

    $attempts = (int) ($_SESSION['admin_login_attempts'] ?? 0);
    $lastAttempt = (int) ($_SESSION['admin_last_failed_login'] ?? 0);

    if ($attempts < $maxAttempts) {
        return false;
    }

    if ((time() - $lastAttempt) >= $lockSeconds) {
        unset(
            $_SESSION['admin_login_attempts'],
            $_SESSION['admin_last_failed_login']
        );

        return false;
    }

    return true;
}

function admin_register_failed_login(): void
{
    $_SESSION['admin_login_attempts'] =
        (int) ($_SESSION['admin_login_attempts'] ?? 0) + 1;

    $_SESSION['admin_last_failed_login'] = time();
}

function admin_clear_login_attempts(): void
{
    unset(
        $_SESSION['admin_login_attempts'],
        $_SESSION['admin_last_failed_login']
    );
}

function build_local_business_schema(array $app, array $settings): array
{
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => $settings['company_name'] ?? 'Рекламное агентство «Джем»',
        'description' => 'Рекламное агентство «Джем» в Абакане занимается изготовлением наружной рекламы, вывесок, баннеров, рекламных конструкций и брендированием автомобилей.',
        'url' => $app['config']['url'],
        'logo' => $app['config']['url'] . '/assets/images/logo/logo.webp',
        'areaServed' => [
            [
                '@type' => 'City',
                'name' => 'Абакан',
            ],
            [
                '@type' => 'AdministrativeArea',
                'name' => 'Республика Хакасия',
            ],
        ],
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Абакан',
            'addressRegion' => 'Республика Хакасия',
            'addressCountry' => 'RU',
        ],
    ];

    if (!empty($settings['phone_1'])) {
        $schema['telephone'] = $settings['phone_1'];
    }

    if (!empty($settings['email'])) {
        $schema['email'] = $settings['email'];
    }

    return $schema;
}