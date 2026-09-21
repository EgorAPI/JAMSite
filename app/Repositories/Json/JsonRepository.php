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
}
