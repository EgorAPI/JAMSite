<?php

declare(strict_types=1);

namespace App\Repositories\Json;

use RuntimeException;

abstract class JsonRepository
{
    public function __construct(protected readonly string $file)
    {
    }

    protected function read(): array
    {
        if (!is_file($this->file)) {
            return [];
        }

        $json = file_get_contents($this->file);
        if ($json === false) {
            throw new RuntimeException('Unable to read JSON data.');
        }

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        return is_array($data) ? $data : [];
    }

    protected function withWriteLock(callable $operation): mixed
    {
        // Keep the lock on a separate file because writes replace the JSON file.
        $lock = fopen($this->file . '.lock', 'c');
        if ($lock === false) {
            throw new RuntimeException('Unable to open JSON lock file.');
        }

        try {
            if (!flock($lock, LOCK_EX)) {
                throw new RuntimeException('Unable to lock JSON data.');
            }

            // Another process may have created the file since our last read.
            clearstatcache(true, $this->file);
            return $operation();
        } finally {
            fclose($lock);
        }
    }

    protected function write(array $data): void
    {
        $contents = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        ) . PHP_EOL;
        $temporaryFile = $this->file . '.' . bin2hex(random_bytes(8)) . '.tmp';

        try {
            if (file_put_contents($temporaryFile, $contents) !== strlen($contents)) {
                throw new RuntimeException('Unable to write temporary JSON data.');
            }

            if (!rename($temporaryFile, $this->file)) {
                throw new RuntimeException('Unable to replace JSON data.');
            }
        } finally {
            if (is_file($temporaryFile)) {
                unlink($temporaryFile);
            }
        }
    }

    protected function appendItem(array $item): array
    {
        return $this->withWriteLock(function () use ($item): array {
            $items = $this->all(false);
            $items[] = $item;
            $this->write($items);

            return $item;
        });
    }

    /**
     * Apply a change to the current record. Returning null skips the write.
     *
     * @param callable(array, array): ?array $change
     */
    public function updateCurrent(string $id, callable $change): ?array
    {
        return $this->withWriteLock(function () use ($id, $change): ?array {
            $items = $this->all(false);

            foreach ($items as $index => $item) {
                if (($item['id'] ?? null) !== $id) {
                    continue;
                }

                $updated = $change($item, $items);
                if ($updated !== null && $updated !== $item) {
                    $items[$index] = $updated;
                    $this->write($items);
                }

                return $updated;
            }

            return null;
        });
    }

    public function delete(string $id): bool
    {
        return $this->withWriteLock(function () use ($id): bool {
            $items = $this->all(false);
            $filteredItems = array_values(array_filter(
                $items,
                static fn (array $item): bool => ($item['id'] ?? null) !== $id
            ));

            if (count($filteredItems) === count($items)) {
                return false;
            }

            $this->write($filteredItems);

            return true;
        });
    }
}
