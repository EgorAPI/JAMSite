<?php

declare(strict_types=1);

return [
    'GET' => [
        '/' => static function (array $app): void {
            admin_require_auth($app);

            render(
                'admin/dashboard',
                ['title' => 'Джем Admin'],
                'admin'
            );
        },

        '/login' => static function (array $app): void {
            if (admin_is_authenticated()) {
                header('Location: /admin/');
                exit;
            }

            render(
                'admin/login',
                ['title' => 'Вход — Джем Admin'],
                'admin'
            );
        },
    ],

    'POST' => [
        '/login' => static function (array $app): void {
            
            if (admin_is_authenticated()) {
                header('Location: /admin/');
                exit;
            }
            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);
                echo 'Недействительный CSRF-токен.';
                return;
            }

            if (admin_login_is_locked($app)) {
                http_response_code(429);

                render(
                    'admin/login',
                    [
                        'title' => 'Вход — Джем Admin',
                        'error' => 'Слишком много неудачных попыток. Попробуйте войти позже.',
                    ],
                    'admin'
                );

                return;
            }

            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            $adminConfig = $app['config']['security']['admin'] ?? [];

            $validUsername = (string) ($adminConfig['username'] ?? '');
            $passwordHash = (string) ($adminConfig['password_hash'] ?? '');

            if ($validUsername === '' || $passwordHash === '') {
                http_response_code(500);

                render(
                    'admin/login',
                    [
                        'title' => 'Вход — Джем Admin',
                        'error' => 'Администратор не настроен. Проверьте конфигурацию окружения.',
                    ],
                    'admin'
                );

                return;
            }

            if (
                $username === $validUsername &&
                $passwordHash !== '' &&
                password_verify($password, $passwordHash)
            ) {
                admin_clear_login_attempts();

                session_regenerate_id(true);

                $_SESSION['admin_authenticated'] = true;
                $_SESSION['admin_last_activity'] = time();

                unset($_SESSION['_csrf']);

                header('Location: /admin/');
                exit;
            }

            admin_register_failed_login();

            http_response_code(422);

            http_response_code(422);

            render(
                'admin/login',
                [
                    'title' => 'Вход — Джем Admin',
                    'error' => 'Неверный логин или пароль.',
                ],
                'admin'
            );
        },

        '/logout' => static function (array $app): void {
            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);
                echo 'Недействительный CSRF-токен.';
                return;
            }

            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();

                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();

            header('Location: /admin/login');
            exit;
        },
    ],
];