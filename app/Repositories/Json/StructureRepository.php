<?php

declare(strict_types=1);

namespace App\Repositories\Json;

use App\Repositories\Contracts\StructureRepositoryInterface;
use RuntimeException;

final class StructureRepository extends JsonRepository implements StructureRepositoryInterface
{
    public function all(bool $visibleOnly = true): array
    {
        $items = $this->read();

        if ($visibleOnly) {
            $items = array_values(array_filter(
                $items,
                fn (array $item): bool => (bool) ($item['is_visible'] ?? false)
            ));
        }

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

        $items[] = $data;

        $this->write($items);

        return $data;
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

    protected function write(array $data): void
    {
        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );

        $temporaryFile = $this->file . '.tmp';

        if (file_put_contents(
            $temporaryFile,
            $json . PHP_EOL,
            LOCK_EX
        ) === false) {
            throw new RuntimeException(
                'Unable to write temporary structures JSON file.'
            );
        }

        if (!rename($temporaryFile, $this->file)) {
            @unlink($temporaryFile);

            throw new RuntimeException(
                'Unable to replace structures JSON file.'
            );
        }
    }
}