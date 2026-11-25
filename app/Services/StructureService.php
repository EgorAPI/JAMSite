<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\StructureRepositoryInterface;

final class StructureService
{
    public function __construct(
        private readonly StructureRepositoryInterface $structures,
        private readonly array $config
    ) {
    }

    public function createSurfacesForType(string $type): array
    {
        $typeConfig = $this->config['types'][$type] ?? null;

        if ($typeConfig === null) {
            throw new \InvalidArgumentException('Unknown structure type: ' . $type);
        }

        $surfaces = [];

        foreach ($typeConfig['surfaces'] as $surfaceConfig) {
            $surface = [
                'id' => 'surf_' . bin2hex(random_bytes(8)),
                'name' => $surfaceConfig['name'],
                'kind' => $surfaceConfig['kind'],
                'image' => null,
            ];

            if ($surfaceConfig['kind'] === 'standard') {
                $surface['status'] = $this->config['default_status'];
            }

            $surfaces[] = $surface;
        }

        return $surfaces;
    }

    public function create(array $data): array
    {
        $type = (string) ($data['type'] ?? '');

        $number = trim((string) ($data['number'] ?? ''));
        $locationDescription = trim((string) ($data['location_description'] ?? ''));

        if ($number === '') {
            throw new \InvalidArgumentException(
                'Structure number is required.'
            );
        }

        if ($locationDescription === '') {
            throw new \InvalidArgumentException(
                'Structure location description is required.'
            );
        }

        $latitude = $this->coordinate($data['latitude'] ?? null, 'Latitude', 90);
        $longitude = $this->coordinate($data['longitude'] ?? null, 'Longitude', 180);
        if (!isset($this->config['types'][$type])) {
            throw new \InvalidArgumentException('Unknown structure type: ' . $type);
        }

        $structure = [
            'id' => 'str_' . bin2hex(random_bytes(8)),
            'number' => $number,
            'type' => $type,
            'location_description' => $locationDescription,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'is_visible' => (
                $latitude !== null
                && $longitude !== null
                && (bool) ($data['is_visible'] ?? true)
            ),
            'surfaces' => $this->createSurfacesForType($type),
        ];

        return $this->structures->create($structure);
    }

    public function all(bool $visibleOnly = true): array
    {
        return $this->structures->all($visibleOnly);
    }

    public function find(string $id): ?array
    {
        return $this->structures->find($id);
    }

    public function update(string $id, array $data): ?array
    {
        return $this->structures->updateCurrent($id, function (array $structure) use ($data): array {
            if (array_key_exists('number', $data)) {
                $structure['number'] = trim((string) $data['number']);

                if ($structure['number'] === '') {
                    throw new \InvalidArgumentException('Structure number is required.');
                }
            }

            if (array_key_exists('location_description', $data)) {
                $structure['location_description'] = trim((string) $data['location_description']);

                if ($structure['location_description'] === '') {
                    throw new \InvalidArgumentException('Structure location description is required.');
                }
            }

            if (array_key_exists('latitude', $data)) {
                $structure['latitude'] = $this->coordinate($data['latitude'], 'Latitude', 90);
            }
            if (array_key_exists('longitude', $data)) {
                $structure['longitude'] = $this->coordinate($data['longitude'], 'Longitude', 180);
            }

            if (array_key_exists('is_visible', $data)) {
                $structure['is_visible'] = (bool) $data['is_visible'];
            }
            if (!isset($structure['latitude'], $structure['longitude'])) {
                $structure['is_visible'] = false;
            }

            if (isset($data['type']) && $data['type'] !== ($structure['type'] ?? null)) {
                $structure['surfaces'] = $this->createSurfacesForType($data['type']);
                $structure['type'] = $data['type'];
            }

            return $structure;
        });
    }

    private function coordinate(mixed $value, string $name, int $limit): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException("$name must be numeric.");
        }

        $coordinate = (float) $value;
        if (!is_finite($coordinate) || $coordinate < -$limit || $coordinate > $limit) {
            throw new \InvalidArgumentException("$name must be between -$limit and $limit.");
        }

        return $coordinate;
    }

    public function delete(string $id): bool
    {
        return $this->structures->delete($id);
    }

    public function updateSurfaceStatus(
        string $structureId,
        string $surfaceId,
        string $status
    ): ?array {
        if (!isset($this->config['statuses'][$status])) {
            throw new \InvalidArgumentException(
                'Unknown structure status: ' . $status
            );
        }

        return $this->updateSurface($structureId, $surfaceId, static function (array $surface) use ($status): array {
            if (($surface['kind'] ?? null) !== 'standard') {
                throw new \InvalidArgumentException(
                    'Status can only be changed for standard surfaces.'
                );
            }

            $surface['status'] = $status;
            return $surface;
        });
    }

    public function updateSurfaceImage(
        string $structureId,
        string $surfaceId,
        ?string $image
    ): ?array {
        return $this->updateSurface($structureId, $surfaceId, static function (array $surface) use ($image): array {
            $surface['image'] = $image;
            return $surface;
        });
    }

    private function updateSurface(string $structureId, string $surfaceId, callable $change): ?array
    {
        return $this->structures->updateCurrent($structureId, static function (array $structure) use ($surfaceId, $change): ?array {
            foreach ($structure['surfaces'] ?? [] as $index => $surface) {
                if (($surface['id'] ?? null) === $surfaceId) {
                    $structure['surfaces'][$index] = $change($surface);
                    return $structure;
                }
            }

            return null;
        });
    }

    public function hasFreeSurface(array $structure): bool
    {
        foreach ($structure['surfaces'] ?? [] as $surface) {
            if (
                ($surface['kind'] ?? null) === 'standard'
                && ($surface['status'] ?? null) === 'free'
            ) {
                return true;
            }
        }

        return false;
    }

    public function free(bool $visibleOnly = true): array
    {
        return array_values(array_filter(
            $this->structures->all($visibleOnly),
            fn (array $structure): bool => $this->hasFreeSurface($structure)
        ));
    }
}
