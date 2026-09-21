<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface PortfolioRepositoryInterface
{
    public function all(bool $activeOnly = true): array;
    public function featured(): array;
    public function find(string $id): ?array;
    public function create(array $data): array;
    public function update(string $id, array $data): ?array;
    public function delete(string $id): bool;
}
