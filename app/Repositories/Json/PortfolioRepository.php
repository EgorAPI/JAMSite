<?php

declare(strict_types=1);

namespace App\Repositories\Json;

use App\Repositories\Contracts\PortfolioRepositoryInterface;

final class PortfolioRepository extends JsonRepository implements PortfolioRepositoryInterface
{
    public function all(bool $activeOnly = true): array
    {
        $items = $this->read();

        if ($activeOnly) {
            $items = array_values(array_filter($items, fn (array $item): bool => (bool) ($item['active'] ?? false)));
        }

        usort($items, fn (array $a, array $b): int => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));
        return $items;
    }

    public function featured(): array
    {
        return array_values(array_filter(
            $this->all(true),
            fn (array $item): bool => (bool) ($item['featured'] ?? false)
        ));
    }

    public function find(string $id): ?array
    {
        foreach ($this->all(false) as $item) {
            if (($item['id'] ?? null) === $id) {
                return $item;
            }
        }

        return null;
    }
}
