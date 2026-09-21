<?php

declare(strict_types=1);

return [
    'name' => 'Рекламное агентство «Джем»',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
    'url' => rtrim(getenv('APP_URL') ?: '', '/'),
    'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Krasnoyarsk',
    'session' => [
        'name' => 'jem_session',
        'timeout' => 1800,
    ],
    'paths' => [
        'data' => dirname(__DIR__) . '/data',
        'uploads' => dirname(__DIR__) . '/public/uploads',
        'logs' => dirname(__DIR__) . '/storage/logs',
        'tmp' => dirname(__DIR__) . '/storage/tmp',
    ],
];
