<?php

declare(strict_types=1);

namespace App\Repositories\Json;

use App\Repositories\Contracts\CategoryRepositoryInterface;

final class CategoryRepository extends JsonRepository implements CategoryRepositoryInterface
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
                ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0)
        );

        return $items;
    }

    public function find(string $id): ?array
    {
        foreach ($this->all(false) as $category) {
            if (($category['id'] ?? null) === $id) {
                return $category;
            }
        }

        return null;
    }

    public function create(array $data): array
    {
        $item = [
            'id' => $data['id'],
            'title' => $data['title'],
            'active' => (bool) ($data['active'] ?? true),
            'sort' => (int) ($data['sort'] ?? 0),
        ];

        return $this->appendItem($item);
    }

    public function update(string $id, array $data): ?array
    {
        return $this->updateCurrent($id, static fn (array $item): array => array_merge($item, $data));
    }
}
