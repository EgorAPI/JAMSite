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

        foreach ($this->structures->all(false) as $existingStructure) {
            if (
                (string) ($existingStructure['number'] ?? '') === $number
                && (string) ($existingStructure['type'] ?? '') === $type
            ) {
                throw new \InvalidArgumentException(
                    'Конструкция с таким номером и типом уже существует.'
                );
            }
        }

        if ($locationDescription === '') {
            throw new \InvalidArgumentException(
                'Structure location description is required.'
            );
        }

        $latitude = null;
        $longitude = null;

        if (isset($data['latitude']) && $data['latitude'] !== '') {
            if (!is_numeric($data['latitude'])) {
                throw new \InvalidArgumentException(
                    'Latitude must be numeric.'
                );
            }

            $latitude = (float) $data['latitude'];

            if ($latitude < -90 || $latitude > 90) {
                throw new \InvalidArgumentException(
                    'Latitude must be between -90 and 90.'
                );
            }
        }

        if (isset($data['longitude']) && $data['longitude'] !== '') {
            if (!is_numeric($data['longitude'])) {
                throw new \InvalidArgumentException(
                    'Longitude must be numeric.'
                );
            }

            $longitude = (float) $data['longitude'];

            if ($longitude < -180 || $longitude > 180) {
                throw new \InvalidArgumentException(
                    'Longitude must be between -180 and 180.'
                );
            }
        }
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
        $structure = $this->structures->find($id);

        if ($structure === null) {
            return null;
        }

        $allowedFields = [
            'number',
            'location_description',
            'latitude',
            'longitude',
            'is_visible',
        ];

        if (array_key_exists('number', $data)) {
        $number = trim((string) $data['number']);

        if ($number === '') {
            throw new \InvalidArgumentException(
                'Structure number is required.'
            );
        }

        $currentType = (string) ($structure['type'] ?? '');

        foreach ($this->structures->all(false) as $existingStructure) {
            if (
                ($existingStructure['id'] ?? '') !== $id
                && (string) ($existingStructure['number'] ?? '') === $number
                && (string) ($existingStructure['type'] ?? '') === $currentType
            ) {
                throw new \InvalidArgumentException(
                    'Конструкция с таким номером и типом уже существует.'
                );
            }
        }

        $data['number'] = $number;
    }

    if (array_key_exists('location_description', $data)) {
        $locationDescription = trim(
            (string) $data['location_description']
        );

        if ($locationDescription === '') {
            throw new \InvalidArgumentException(
                'Structure location description is required.'
            );
        }

        $data['location_description'] = $locationDescription;
    }

        if (array_key_exists('latitude', $data)) {
        if (!is_numeric($data['latitude'])) {
            throw new \InvalidArgumentException(
                'Valid latitude is required.'
            );
        }

        $latitude = (float) $data['latitude'];

        if ($latitude < -90 || $latitude > 90) {
            throw new \InvalidArgumentException(
                'Latitude must be between -90 and 90.'
            );
        }
    }

    if (array_key_exists('longitude', $data)) {
        if (!is_numeric($data['longitude'])) {
            throw new \InvalidArgumentException(
                'Valid longitude is required.'
            );
        }

        $longitude = (float) $data['longitude'];

        if ($longitude < -180 || $longitude > 180) {
            throw new \InvalidArgumentException(
                'Longitude must be between -180 and 180.'
            );
        }
    }

        $finalLatitude = array_key_exists('latitude', $data)
            ? (float) $data['latitude']
            : ($structure['latitude'] ?? null);

        $finalLongitude = array_key_exists('longitude', $data)
            ? (float) $data['longitude']
            : ($structure['longitude'] ?? null);

        if (
            !empty($data['is_visible'])
            && ($finalLatitude === null || $finalLongitude === null)
        ) {
            $data['is_visible'] = false;
        }

        $updateData = [];

        foreach ($allowedFields as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $updateData[$field] = match ($field) {
                'number', 'location_description' => (string) $data[$field],
                'latitude', 'longitude' => (float) $data[$field],
                'is_visible' => (bool) $data[$field],
                default => $data[$field],
            };
        }

        return $this->structures->update($id, $updateData);
    }

    public function delete(string $id): bool
    {
        return $this->structures->delete($id);
    }

    public function changeType(string $id, string $newType): ?array
    {
        $structure = $this->structures->find($id);

        if ($structure === null) {
            return null;
        }

        if (!isset($this->config['types'][$newType])) {
            throw new \InvalidArgumentException(
                'Unknown structure type: ' . $newType
            );
        }

        if (($structure['type'] ?? null) === $newType) {
            return $structure;
        }

        $number = (string) ($structure['number'] ?? '');

        foreach ($this->structures->all(false) as $existingStructure) {
            if (
                ($existingStructure['id'] ?? '') !== $id
                && (string) ($existingStructure['number'] ?? '') === $number
                && (string) ($existingStructure['type'] ?? '') === $newType
            ) {
                throw new \InvalidArgumentException(
                    'Конструкция с таким номером и типом уже существует.'
                );
            }
        }

        return $this->structures->update($id, [
            'type' => $newType,
            'surfaces' => $this->createSurfacesForType($newType),
        ]);
    }

    public function updateSurfaceStatus(
        string $structureId,
        string $surfaceId,
        string $status
    ): ?array {
        if (!isset($this->config['statuses'][$status])) {
            throw new \InvalidArgumentException(
                'Unknown surface status: ' . $status
            );
        }

        $structure = $this->structures->find($structureId);

        if ($structure === null) {
            return null;
        }

        $surfaces = $structure['surfaces'] ?? [];

        foreach ($surfaces as $index => $surface) {
            if (($surface['id'] ?? null) !== $surfaceId) {
                continue;
            }

            if (($surface['kind'] ?? null) !== 'standard') {
                throw new \InvalidArgumentException(
                    'Status can only be changed for standard surfaces.'
                );
            }

            $surfaces[$index]['status'] = $status;

            return $this->structures->update($structureId, [
                'surfaces' => $surfaces,
            ]);
        }

        return null;
    }

    public function updateSurfaceImage(
        string $structureId,
        string $surfaceId,
        ?string $image
    ): ?array {
        $structure = $this->structures->find($structureId);

        if ($structure === null) {
            return null;
        }

        foreach ($structure['surfaces'] as &$surface) {
            if (($surface['id'] ?? '') !== $surfaceId) {
                continue;
            }

            $surface['image'] = $image;

            return $this->structures->update(
                $structureId,
                [
                    'surfaces' => $structure['surfaces'],
                ]
            );
        }

        return null;
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