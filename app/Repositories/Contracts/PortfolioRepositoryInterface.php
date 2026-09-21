<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface PortfolioRepositoryInterface
{
    public function all(bool $activeOnly = true): array;
    public function featured(): array;
    public function find(string $id): ?array;
}
