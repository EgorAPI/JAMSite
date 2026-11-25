<?php

declare(strict_types=1);

namespace App\Repositories\Json;

use App\Repositories\Contracts\StructureRepositoryInterface;
use InvalidArgumentException;
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
        return $this->withWriteLock(function () use ($data): array {
            $items = $this->read();
            $this->ensureUnique($items, $data);

            $items[] = $data;
            $this->write($items);

            return $data;
        });
    }

    public function update(string $id, array $data): ?array
    {
        return $this->updateCurrent($id, static fn (array $item): array => array_merge($item, $data));
    }

    public function updateCurrent(string $id, callable $change): ?array
    {
        return parent::updateCurrent($id, function (array $item, array $items) use ($change, $id): ?array {
            $updated = $change($item);
            if ($updated !== null && (
                ($updated['number'] ?? null) !== ($item['number'] ?? null)
                || ($updated['type'] ?? null) !== ($item['type'] ?? null)
            )) {
                $this->ensureUnique($items, $updated, $id);
            }

            return $updated;
        });
    }

    private function ensureUnique(array $items, array $candidate, ?string $excludedId = null): void
    {
        if (!isset($candidate['number'], $candidate['type'])) {
            return;
        }

        foreach ($items as $item) {
            if (
                ($excludedId === null || ($item['id'] ?? null) !== $excludedId)
                && (string) ($item['number'] ?? '') === (string) $candidate['number']
                && ($item['type'] ?? null) === $candidate['type']
            ) {
                throw new InvalidArgumentException(
                    'Конструкция с таким номером и типом уже существует.'
                );
            }
        }
    }

    public function replaceAll(array $structures, string $backupDirectory): void
    {
        $this->withWriteLock(function () use ($structures, $backupDirectory): void {
            if (is_file($this->file)) {
                $backupFile = $backupDirectory . '/structures-'
                    . date('Y-m-d-His') . '-' . bin2hex(random_bytes(3)) . '.json';

                if (!is_dir($backupDirectory) || !copy($this->file, $backupFile)) {
                    throw new RuntimeException(
                        'Не удалось создать резервную копию текущих конструкций.'
                    );
                }
            }

            $this->write($structures);
        });
    }
}
