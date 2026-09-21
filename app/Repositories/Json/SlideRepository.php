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
}