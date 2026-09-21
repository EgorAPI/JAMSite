<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ContactRequestController;

return [
    'GET' => [
        '/' => HomeController::class,
        '/portfolio' => PortfolioController::class,
        '/contacts' => static fn (array $app) => render('pages/contacts', ['title' => 'Контакты — «Джем»']),
        '/privacy' => static fn (array $app) => render('pages/privacy', ['title' => 'Политика конфиденциальности']),
    ],
    'POST' => [
        '/contact-request' => ContactRequestController::class,
    ],
];
