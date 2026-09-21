<?php

declare(strict_types=1);

namespace App\Repositories\Json;

use App\Repositories\Contracts\CategoryRepositoryInterface;

final class CategoryRepository implements CategoryRepositoryInterface
{
    private string $file;

 public function __construct(string $file)
{
    $this->file = $file;
}

    public function all(bool $activeOnly = true): array
    {
        if (!file_exists($this->file)) {
            return [];
        }

        $json = file_get_contents($this->file);

        if ($json === false || $json === '') {
            return [];
        }

        $items = json_decode($json, true);

        if (!is_array($items)) {
            return [];
        }

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
}