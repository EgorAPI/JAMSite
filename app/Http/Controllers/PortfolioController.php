<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\Json\PortfolioRepository;
use App\Repositories\Json\CategoryRepository;
use App\Services\PortfolioService;

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

    

    $portfolioService = new PortfolioService(
        $portfolio,
        $categories
    );
    
    $portfolioItems = $portfolioService->all();


    render('pages/portfolio', [
        'title' => 'Наши работы — «Джем»',
        'portfolioItems' => $portfolioItems,
        'categories' => $categories->all(),
    ]);
}
}
