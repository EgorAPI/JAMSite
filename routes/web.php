<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\StructureController;

return [
    'GET' => [
        '/' => HomeController::class,
        '/services/signs' => static fn (array $app) => render(
            'pages/services/signs',
            [
                'title' => 'Изготовление вывесок в Абакане — рекламное агентство «Джем»',
                'metaDescription' => 'Изготовление вывесок в Абакане: объёмные, псевдообъёмные и световые вывески, световые короба. Разработка макета, изготовление и монтаж.',
                'canonical' => $app['config']['url'] . '/services/signs',
                'ogTitle' => 'Изготовление вывесок в Абакане — «Джем»',
                'ogDescription' => 'Объёмные, псевдообъёмные и световые вывески, световые короба. Изготовление и монтаж в Абакане.',
                'ogUrl' => $app['config']['url'] . '/services/signs',
                'ogImage' => $app['config']['url'] . '/assets/images/services/service-signs.webp',
            ]
        ),
        '/services/large-format-printing' => static fn (array $app) => render(
            'pages/services/large-format-printing',
            [
                'title' => 'Широкоформатная и интерьерная печать в Абакане — «Джем»',
                'metaDescription' => 'Широкоформатная и интерьерная печать в Абакане: баннеры 240–500 г/м², литой баннер, печать на плёнке, изготовление баннерных конструкций.',
                'canonical' => $app['config']['url'] . '/services/large-format-printing',
                'ogTitle' => 'Широкоформатная и интерьерная печать в Абакане — «Джем»',
                'ogDescription' => 'Печать баннеров и на плёнке, интерьерная печать и изготовление баннерных конструкций в Абакане.',
                'ogUrl' => $app['config']['url'] . '/services/large-format-printing',
                'ogImage' => $app['config']['url'] . '/assets/images/services/service-banners.webp',
            ]
        ),
        '/services/car-branding' => static fn (array $app) => render(
            'pages/services/car-branding',
            [
                'title' => 'Брендирование автомобилей в Абакане — «Джем»',
                'metaDescription' => 'Брендирование автомобилей в Абакане: частичная оклейка плёнкой, логотипы, контакты и рекламная графика. Оформление легковых авто и кузовов грузовиков.',
                'canonical' => $app['config']['url'] . '/services/car-branding',
                'ogTitle' => 'Брендирование автомобилей в Абакане — «Джем»',
                'ogDescription' => 'Рекламное оформление автомобилей плёнкой: логотипы, контакты, графика и брендирование кузовов грузовиков.',
                'ogUrl' => $app['config']['url'] . '/services/car-branding',
                'ogImage' => $app['config']['url'] . '/assets/images/services/service-cars.webp',
            ]
        ),
        '/services/plates' => static fn (array $app) => render(
            'pages/services/plates',
            [
                'title' => 'Изготовление табличек и режимников в Абакане — «Джем»',
                'metaDescription' => 'Изготовление табличек в Абакане: информационные, адресные таблички и режимники. ПВХ, композит, оцинковка, оргстекло и акрил.',
                'canonical' => $app['config']['url'] . '/services/plates',
                'ogTitle' => 'Изготовление табличек и режимников в Абакане — «Джем»',
                'ogDescription' => 'Информационные и адресные таблички, режимники различных форм и размеров. Изготовление в Абакане.',
                'ogUrl' => $app['config']['url'] . '/services/plates',
                'ogImage' => $app['config']['url'] . '/assets/images/services/service-plates.webp',
            ]
        ),
        '/services/stands' => static fn (array $app) => render(
            'pages/services/stands',
            [
                'title' => 'Изготовление информационных стендов в Абакане — «Джем»',
                'metaDescription' => 'Изготовление стендов в Абакане: информационные и рекламные стенды, стенды с карманами и крупные конструкции. ПВХ, композит и оцинковка.',
                'canonical' => $app['config']['url'] . '/services/stands',
                'ogTitle' => 'Изготовление информационных стендов в Абакане — «Джем»',
                'ogDescription' => 'Информационные и рекламные стенды, стенды с карманами и крупные конструкции. Изготовление в Абакане.',
                'ogUrl' => $app['config']['url'] . '/services/stands',
                'ogImage' => $app['config']['url'] . '/assets/images/services/service-stands.webp',
            ]
        ),
        '/services/clothing-print' => static fn (array $app) => render(
            'pages/services/clothing-print',
            [
                'title' => 'Печать на одежде в Абакане — рекламное агентство «Джем»',
                'metaDescription' => 'Печать на одежде в Абакане: термотрансферное нанесение логотипов, надписей и графики специальной плёнкой с помощью термопресса.',
                'canonical' => $app['config']['url'] . '/services/clothing-print',
                'ogTitle' => 'Печать на одежде в Абакане — «Джем»',
                'ogDescription' => 'Термотрансферное нанесение логотипов, надписей и графики на одежду с помощью специальной плёнки и термопресса.',
                'ogUrl' => $app['config']['url'] . '/services/clothing-print',
                'ogImage' => $app['config']['url'] . '/assets/images/services/service-print.webp',
            ]
        ),
        '/portfolio' => PortfolioController::class,
        '/structures' => StructureController::class,
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
