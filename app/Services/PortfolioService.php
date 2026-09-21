<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\PortfolioRepositoryInterface;

final class PortfolioService
{
    public function __construct(
        private PortfolioRepositoryInterface $portfolio,
        private CategoryRepositoryInterface $categories
    ) {
    }

    public function all(): array
    {
        return $this->attachCategoryTitles(
            $this->portfolio->all()
        );
    }

    public function featured(): array
    {
        return $this->attachCategoryTitles(
            $this->portfolio->featured()
        );
    }

    public function find(string $id): ?array
    {
        $item = $this->portfolio->find($id);

        if ($item === null) {
            return null;
        }

        return $this->attachCategoryTitles([$item])[0] ?? null;
    }

    private function attachCategoryTitles(array $items): array
    {
        foreach ($items as &$item) {
            $category = $this->categories->find(
                $item['category'] ?? ''
            );

            $item['category_title'] = $category['title'] ?? '';
        }

        unset($item);

        return $items;
    }
}