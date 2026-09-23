<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\Json\CategoryRepository;
use App\Repositories\Json\PortfolioRepository;
use App\Repositories\Json\SlideRepository;
use App\Services\PortfolioService;
use App\Repositories\Json\SettingsRepository;

final class HomeController
{
    public function __invoke(array $app): void
    {
        $portfolio = new PortfolioRepository(
            $app['config']['paths']['data'] . '/portfolio.json'
        );

        $categories = new CategoryRepository(
            $app['config']['paths']['data'] . '/categories.json'
        );

        $slides = new SlideRepository(
            $app['config']['paths']['data'] . '/slides.json'
        );

        $portfolioService = new PortfolioService(
            $portfolio,
            $categories
        );
        $settings = new SettingsRepository(
            $app['config']['paths']['data'] . '/settings.json'
        );

        $featuredPortfolioItems = $portfolioService->featured();

        $settingsData = $settings->get();
        $localBusinessSchema = build_local_business_schema(
            $app,
            $settingsData
        );


        render('pages/home', [
            'title' => 'Наружная реклама в Абакане — рекламное агентство «Джем»',
            'metaDescription' => 'Рекламное агентство «Джем» в Абакане: наружная реклама, вывески, баннеры, брендирование автомобилей, печать и изготовление рекламных конструкций.',
            'canonical' => $app['config']['url'] . '/',
            'ogTitle' => 'Наружная реклама в Абакане — рекламное агентство «Джем»',
            'ogDescription' => 'Рекламное агентство «Джем» в Абакане: наружная реклама, вывески, баннеры, брендирование автомобилей, печать и изготовление рекламных конструкций.',
            'ogUrl' => $app['config']['url'] . '/',
            'ogImage' => $app['config']['url'] . '/assets/images/logo/logo.webp',
            'featuredPortfolioItems' => $featuredPortfolioItems,
            'slides' => $slides->all(),
            'settings' => $settingsData,
            'localBusinessSchema' => $localBusinessSchema,
        ]);
    }
}