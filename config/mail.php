<?php

declare(strict_types=1);

return [
    'host' => $_ENV['MAIL_HOST'] ?? '',
    'port' => (int) ($_ENV['MAIL_PORT'] ?? 465),
    'username' => $_ENV['MAIL_USERNAME'] ?? '',
    'password' => $_ENV['MAIL_PASSWORD'] ?? '',
    'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'ssl',
    'from' => $_ENV['MAIL_FROM'] ?? '',
    'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Рекламное агентство «Джем»',
    'to' => $_ENV['MAIL_TO'] ?? '',
];