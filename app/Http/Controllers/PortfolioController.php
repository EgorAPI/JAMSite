<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\Json\PortfolioRepository;
use App\Repositories\Json\CategoryRepository;
use App\Services\PortfolioService;
use App\Repositories\Json\SettingsRepository;

final class PortfolioController
{
public function __invoke(array $app): void
{
    $portfolio = new PortfolioRepository(
        $app['config']['paths']['data'] . '/portfolio.json'
    );

    $categories = new CategoryRepository(
        $app['config']['paths']['data'] . '/categories.json'
    );

    $settings = new SettingsRepository(
    $app['config']['paths']['data'] . '/settings.json'
    );

    $settingsData = $settings->get();

    $localBusinessSchema = build_local_business_schema(
        $app,
        $settingsData
    );

    

    $portfolioService = new PortfolioService(
        $portfolio,
        $categories
    );
    
    $portfolioItems = $portfolioService->all();


    render('pages/portfolio', [
        'title' => 'Портфолио рекламного агентства «Джем» — Абакан',
        'metaDescription' => 'Портфолио рекламного агентства «Джем» в Абакане: вывески, баннеры, брендирование автомобилей, стенды, таблички и другие выполненные работы.',
        'canonical' => $app['config']['url'] . '/portfolio',
        'ogTitle' => 'Портфолио рекламного агентства «Джем» — Абакан',
        'ogDescription' => 'Портфолио рекламного агентства «Джем» в Абакане: вывески, баннеры, брендирование автомобилей, стенды, таблички и другие выполненные работы.',
        'ogUrl' => $app['config']['url'] . '/portfolio',
        'ogImage' => $app['config']['url'] . '/assets/images/logo/logo.webp',
        'portfolioItems' => $portfolioItems,
        'categories' => $categories->all(),
        'settings' => $settingsData,
        'localBusinessSchema' => $localBusinessSchema,
    ]);
}
}
