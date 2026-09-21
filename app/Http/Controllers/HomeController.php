<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\Json\CategoryRepository;
use App\Repositories\Json\PortfolioRepository;
use App\Repositories\Json\SlideRepository;
use App\Services\PortfolioService;

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

        $featuredPortfolioItems = $portfolioService->featured();

        render('pages/home', [
            'title' => 'Рекламное агентство «Джем» — Абакан',
            'featuredPortfolioItems' => $featuredPortfolioItems,
            'slides' => $slides->all(),
        ]);
    }
}