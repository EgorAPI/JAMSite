<?php

declare(strict_types=1);

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;

final class StructureImportService
{
    private const TYPE_MAP = [
        'билборд' => 'billboard',
        'призматрон' => 'prismatron',
        'ситиборд' => 'cityboard',
        'ситискроллер' => 'cityscroller',
        'пилар' => 'pillar',
        'световой короб' => 'lightbox',
        'брендмауэр' => 'brandmauer',
    ];

    private const REQUIRED_HEADERS = [
        'Широта',
        'Долгота',
        'Описание',
        'Номер метки',
        'Тип конструкции',
    ];

    public function __construct(
        private readonly array $config
    ) {
    }

    private function normalizeText(mixed $value): string
    {
        $value = trim((string) $value);

        return mb_strtolower($value, 'UTF-8');
    }

    private function loadWorksheet(string $filePath): \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
    {
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException(
                'Файл Excel не найден.'
            );
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
        } catch (\Throwable $exception) {
            throw new \InvalidArgumentException(
                'Не удалось прочитать файл Excel.'
            );
        }

        return $spreadsheet->getActiveSheet();
    }

    private function validateHeaders(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
    ): void {
        $highestColumn = $worksheet->getHighestDataColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString(
            $highestColumn
        );

        if ($highestColumnIndex !== count(self::REQUIRED_HEADERS)) {
            throw new \InvalidArgumentException(
                'Неверная структура Excel-файла. Должно быть ровно '
                . count(self::REQUIRED_HEADERS)
                . ' столбцов.'
            );
        }

        $actualHeaders = [];

        for ($column = 1; $column <= count(self::REQUIRED_HEADERS); $column++) {
            $actualHeaders[] = trim(
                (string) $worksheet->getCell([$column, 1])->getValue()
            );
        }

        if ($actualHeaders !== self::REQUIRED_HEADERS) {
            throw new \InvalidArgumentException(
                'Неверная структура Excel-файла. Ожидаются столбцы: '
                . implode(', ', self::REQUIRED_HEADERS)
                . '.'
            );
        }
    }

    private function resolveType(mixed $value, int $row): string
    {
        $typeName = $this->normalizeText($value);

        if ($typeName === '') {
            throw new \InvalidArgumentException(
                'Строка ' . $row . ': не указан тип конструкции.'
            );
        }

        if (!isset(self::TYPE_MAP[$typeName])) {
            throw new \InvalidArgumentException(
                'Строка ' . $row
                . ': неизвестный тип конструкции «'
                . trim((string) $value)
                . '».'
            );
        }

        $type = self::TYPE_MAP[$typeName];

        if (!isset($this->config['types'][$type])) {
            throw new \InvalidArgumentException(
                'Строка ' . $row
                . ': тип конструкции отсутствует в конфигурации сайта.'
            );
        }

        return $type;
    }

    private function validateCoordinate(
        mixed $value,
        int $row,
        string $name,
        float $min,
        float $max
    ): float {
        if ($value === null || trim((string) $value) === '') {
            throw new \InvalidArgumentException(
                'Строка ' . $row . ': не указано поле «' . $name . '».'
            );
        }

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException(
                'Строка ' . $row . ': поле «' . $name . '» должно быть числом.'
            );
        }

        $coordinate = (float) $value;

        if ($coordinate < $min || $coordinate > $max) {
            throw new \InvalidArgumentException(
                'Строка ' . $row . ': некорректное значение поля «' . $name . '».'
            );
        }

        return $coordinate;
    }

    private function requireText(
        mixed $value,
        int $row,
        string $name
    ): string {
        $text = trim((string) $value);

        if ($text === '') {
            throw new \InvalidArgumentException(
                'Строка ' . $row . ': не заполнено поле «' . $name . '».'
            );
        }

        return $text;
    }

    private function readNumber(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet,
        int $row
    ): string {
        $cell = $worksheet->getCell([4, $row]);

        $number = trim((string) $cell->getFormattedValue());

        if ($number === '') {
            throw new \InvalidArgumentException(
                'Строка ' . $row . ': не заполнено поле «Номер метки».'
            );
        }

        return $number;
    }

    private function parseRow(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet,
        int $row
    ): array {
        $latitude = $this->validateCoordinate(
            $worksheet->getCell([1, $row])->getValue(),
            $row,
            'Широта',
            -90,
            90
        );

        $longitude = $this->validateCoordinate(
            $worksheet->getCell([2, $row])->getValue(),
            $row,
            'Долгота',
            -180,
            180
        );

        $description = $this->requireText(
            $worksheet->getCell([3, $row])->getValue(),
            $row,
            'Описание'
        );

        $number = $this->readNumber($worksheet, $row);

        $type = $this->resolveType(
            $worksheet->getCell([5, $row])->getValue(),
            $row
        );

        return [
            'number' => $number,
            'type' => $type,
            'location_description' => $description,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }

    private function createSurfaces(string $type): array
    {
        $typeConfig = $this->config['types'][$type] ?? null;

        if ($typeConfig === null) {
            throw new \InvalidArgumentException(
                'Неизвестный тип конструкции: ' . $type
            );
        }

        $surfaces = [];

        foreach ($typeConfig['surfaces'] as $surfaceConfig) {
            $surface = [
                'id' => 'surf_' . bin2hex(random_bytes(8)),
                'name' => (string) $surfaceConfig['name'],
                'kind' => (string) $surfaceConfig['kind'],
                'image' => null,
            ];

            if ($surface['kind'] === 'standard') {
                $surface['status'] = 'unavailable';
            }

            $surfaces[] = $surface;
        }

        return $surfaces;
    }

    private function buildStructure(array $row): array
    {
        return [
            'id' => 'str_' . bin2hex(random_bytes(8)),
            'number' => $row['number'],
            'type' => $row['type'],
            'location_description' => $row['location_description'],
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude'],
            'is_visible' => true,
            'surfaces' => $this->createSurfaces($row['type']),
        ];
    }

    public function prepare(string $filePath): array
    {
        $worksheet = $this->loadWorksheet($filePath);

        $this->validateHeaders($worksheet);

        $structures = [];
        $uniqueKeys = [];

        $highestRow = $worksheet->getHighestDataRow();

        if ($highestRow > 5001) {
            throw new \InvalidArgumentException(
                'Excel-файл содержит слишком много строк. Максимум — 5000 конструкций.'
            );
        }

        for ($row = 2; $row <= $highestRow; $row++) {
            $rowIsEmpty = true;

            for ($column = 1; $column <= 5; $column++) {
                $value = $worksheet->getCell([$column, $row])->getValue();

                if ($value !== null && trim((string) $value) !== '') {
                    $rowIsEmpty = false;
                    break;
                }
            }

            if ($rowIsEmpty) {
                continue;
            }

            $parsedRow = $this->parseRow($worksheet, $row);

            $uniqueKey = $parsedRow['number'] . '|' . $parsedRow['type'];

            if (isset($uniqueKeys[$uniqueKey])) {
                throw new \InvalidArgumentException(
                    'Строка ' . $row
                    . ': конструкция с номером «'
                    . $parsedRow['number']
                    . '» и таким типом уже встречалась в строке '
                    . $uniqueKeys[$uniqueKey]
                    . '.'
                );
            }

            $uniqueKeys[$uniqueKey] = $row;

            $structures[] = $this->buildStructure($parsedRow);
        }

        if ($structures === []) {
            throw new \InvalidArgumentException(
                'Excel-файл не содержит конструкций.'
            );
        }

        return $structures;
    }

    private function createBackup(string $structuresFile, string $backupDirectory): ?string
    {
        if (!is_file($structuresFile)) {
            return null;
        }

        if (!is_dir($backupDirectory)) {
            throw new \RuntimeException(
                'Папка резервных копий не найдена.'
            );
        }

        $backupFile = $backupDirectory
            . '/structures-'
            . date('Y-m-d-His')
            . '-'
            . bin2hex(random_bytes(3))
            . '.json';

        if (!copy($structuresFile, $backupFile)) {
            throw new \RuntimeException(
                'Не удалось создать резервную копию текущих конструкций.'
            );
        }

        return $backupFile;
    }

    private function replaceStructuresFile(
        string $structuresFile,
        array $structures
    ): void {
        try {
            $json = json_encode(
                $structures,
                JSON_PRETTY_PRINT
                | JSON_UNESCAPED_UNICODE
                | JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $exception) {
            throw new \RuntimeException(
                'Не удалось подготовить новые данные конструкций.'
            );
        }

        $temporaryFile = $structuresFile
            . '.import-'
            . bin2hex(random_bytes(4))
            . '.tmp';

        if (file_put_contents($temporaryFile, $json . PHP_EOL, LOCK_EX) === false) {
            throw new \RuntimeException(
                'Не удалось записать временный файл импорта.'
            );
        }

        if (!rename($temporaryFile, $structuresFile)) {
            @unlink($temporaryFile);

            throw new \RuntimeException(
                'Не удалось заменить файл конструкций.'
            );
        }
    }

    public function import(
        string $excelFile,
        string $structuresFile,
        string $backupDirectory
    ): int {
        // Сначала полностью читаем и проверяем Excel.
        // До завершения этого этапа текущие данные не изменяются.
        $structures = $this->prepare($excelFile);

        // Только после успешной проверки всего Excel
        // сохраняем текущую базу.
        $this->createBackup(
            $structuresFile,
            $backupDirectory
        );

        // Полностью заменяем structures.json новым каталогом.
        $this->replaceStructuresFile(
            $structuresFile,
            $structures
        );

        return count($structures);
    }
}