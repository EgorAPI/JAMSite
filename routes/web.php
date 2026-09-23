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
        '/privacy' => static fn (array $app) => render(
            'pages/privacy',
            [
                'title' => 'Политика конфиденциальности — «Джем»',
                'metaDescription' => 'Политика конфиденциальности рекламного агентства «Джем» и условия обработки персональных данных пользователей сайта.',
                'canonical' => $app['config']['url'] . '/privacy',
                'ogTitle' => 'Политика конфиденциальности — «Джем»',
                'ogDescription' => 'Политика конфиденциальности рекламного агентства «Джем» и условия обработки персональных данных пользователей сайта.',
                'ogUrl' => $app['config']['url'] . '/privacy',
                'ogImage' => $app['config']['url'] . '/assets/images/logo/logo.webp',
            ]
        ),
    ],
    'POST' => [
        '/contact-request' => ContactRequestController::class,
    ],
];
