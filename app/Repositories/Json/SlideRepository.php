<?php

declare(strict_types=1);

namespace App\Repositories\Json;

use App\Repositories\Contracts\SlideRepositoryInterface;

final class SlideRepository extends JsonRepository implements SlideRepositoryInterface
{
    public function all(bool $activeOnly = true): array
    {
        $items = $this->read();

        if ($activeOnly) {
            $items = array_values(array_filter(
                $items,
                fn (array $item): bool => (bool) ($item['active'] ?? false)
            ));
        }

        usort(
            $items,
            fn (array $a, array $b): int =>
                ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0)
        );

        return $items;
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
            'image' => $data['image'],
            'active' => (bool) ($data['active'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
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
    public function delete(string $id): bool
    {
        $items = $this->all(false);

        $filteredItems = array_values(array_filter(
            $items,
            fn (array $item): bool => ($item['id'] ?? null) !== $id
        ));

        if (count($filteredItems) === count($items)) {
            return false;
        }

        $this->write($filteredItems);

        return true;
    }
}