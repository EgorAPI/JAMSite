<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface CategoryRepositoryInterface
{
    public function all(bool $activeOnly = true): array;

    public function find(string $id): ?array;
}