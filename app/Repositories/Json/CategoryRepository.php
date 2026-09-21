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
    public function create(array $data): array
    {
        $items = $this->all(false);

        $item = [
            'id' => $data['id'],
            'title' => $data['title'],
            'active' => (bool) ($data['active'] ?? true),
            'sort' => (int) ($data['sort'] ?? 0),
        ];

        $items[] = $item;

        $json = json_encode(
            $items,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );

        if (file_put_contents($this->file, $json . PHP_EOL, LOCK_EX) === false) {
            throw new \RuntimeException('Unable to write categories JSON.');
        }

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

            $json = json_encode(
                $items,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            );

            if (file_put_contents($this->file, $json . PHP_EOL, LOCK_EX) === false) {
                throw new \RuntimeException('Unable to write categories JSON.');
            }

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

        $json = json_encode(
            $filteredItems,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );

        if (file_put_contents($this->file, $json . PHP_EOL, LOCK_EX) === false) {
            throw new \RuntimeException('Unable to write categories JSON.');
        }

        return true;
    }
}