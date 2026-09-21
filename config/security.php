<?php

declare(strict_types=1);

return [
    'csrf_key' => '_csrf',

    'admin' => [
        'username' => $_ENV['ADMIN_USERNAME'] ?? '',
        'password_hash' => $_ENV['ADMIN_PASSWORD_HASH'] ?? '',
        'session_timeout' => 60 * 60,
        'max_login_attempts' => 5,
        'login_lock_seconds' => 300,
    ],

    'upload_max_bytes' => 10 * 1024 * 1024,

    'allowed_image_mime' => [
        'image/jpeg',
        'image/png',
        'image/webp',
    ],
];