<?php

declare(strict_types=1);

namespace App\Repositories\Json;

final class SettingsRepository extends JsonRepository
{
    public function get(): array
    {
        return $this->read();
    }

    public function update(array $data): array
    {
        $settings = array_merge(
            $this->get(),
            $data
        );

        $this->write($settings);

        return $settings;
    }
}