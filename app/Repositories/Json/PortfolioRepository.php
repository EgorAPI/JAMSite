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

    public function create(array $data): array
    {
        $items = $this->all(false);

        $item = [
            'id' => $data['id'],
            'title' => $data['title'],
            'category' => $data['category'],
            'image' => $data['image'],
            'active' => (bool) ($data['active'] ?? false),
            'featured' => (bool) ($data['featured'] ?? false),
            'sort' => (int) ($data['sort'] ?? 0),
        ];

        $items[] = $item;

        $this->write($items);

        return $item;
    }
    public function update(string $id, array $data): ?array
    {
        $items = $this->all(false);

        foreach ($items as $index => $item) {
            if (($item['id'] ?? null) !== $id) {
                continue;
            }

            $updatedItem = array_merge($item, $data);

            $items[$index] = $updatedItem;

            $this->write($items);

            return $updatedItem;
        }

        return null;
    }
}
