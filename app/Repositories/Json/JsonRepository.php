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

    protected function write(array $data): void
    {
        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );

        if (file_put_contents($this->file, $json . PHP_EOL, LOCK_EX) === false) {
            throw new RuntimeException('Unable to write JSON data.');
        }
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
