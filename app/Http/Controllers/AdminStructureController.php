<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\Json\StructureRepository;
use App\Services\StructureService;
use App\Services\StructureImageService;
use App\Services\StructureImportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class AdminStructureController
{
    public function index(array $app): void
    {
        $repository = new StructureRepository(
            $app['config']['paths']['data'] . '/structures.json'
        );

        $service = new StructureService(
            $repository,
            $app['config']['structures']
        );

        $structures = $service->all(false);

        $structureTypes = $app['config']['structures']['types'];
        $structureStatuses = $app['config']['structures']['statuses'];

        require dirname(__DIR__, 3) . '/templates/admin/structures/index.php';
    }

    public function create(array $app): void
    {
        $structureTypes = $app['config']['structures']['types'];
        $structureStatuses = $app['config']['structures']['statuses'];

        $error = null;

        require dirname(__DIR__, 3)
            . '/templates/admin/structures/create.php';
    }

    public function store(array $app): void
    {
        $csrfToken = $_POST['_csrf'] ?? null;

        if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
            http_response_code(419);

            echo 'Недействительный CSRF-токен.';
            return;
        }

        $repository = new StructureRepository(
            $app['config']['paths']['data'] . '/structures.json'
        );

        $service = new StructureService(
            $repository,
            $app['config']['structures']
        );

        try {
            $structure = $service->create([
                'number' => trim((string) ($_POST['number'] ?? '')),
                'type' => trim((string) ($_POST['type'] ?? '')),
                'location_description' => trim(
                    (string) ($_POST['location_description'] ?? '')
                ),
                'is_visible' => false,
            ]);
        } catch (\InvalidArgumentException $exception) {
            $error = $exception->getMessage();

            $structureTypes = $app['config']['structures']['types'];
            $structureStatuses = $app['config']['structures']['statuses'];

            require dirname(__DIR__, 3)
                . '/templates/admin/structures/create.php';

            return;
        }

        header(
            'Location: /admin/structures/edit?id='
            . urlencode((string) $structure['id'])
        );
        exit;
    }

    public function edit(array $app): void
    {
        $id = trim((string) ($_GET['id'] ?? ''));

        $repository = new StructureRepository(
            $app['config']['paths']['data'] . '/structures.json'
        );

        $service = new StructureService(
            $repository,
            $app['config']['structures']
        );

        $structure = $service->find($id);

        if ($structure === null) {
            http_response_code(404);
            echo 'Конструкция не найдена.';
            return;
        }

        $structureTypes = $app['config']['structures']['types'];
        $structureStatuses = $app['config']['structures']['statuses'];

        require dirname(__DIR__, 3) . '/templates/admin/structures/edit.php';
    }

    public function update(array $app): void
    {
        $csrfToken = $_POST['_csrf'] ?? null;

        if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
            http_response_code(419);
            echo 'Недействительный CSRF-токен.';
            return;
        }

        $id = trim((string) ($_POST['id'] ?? ''));

        $repository = new StructureRepository(
            $app['config']['paths']['data'] . '/structures.json'
        );

        $service = new StructureService(
            $repository,
            $app['config']['structures']
        );

        $imageService = new StructureImageService();

        $structure = $service->find($id);

        if ($structure === null) {
            http_response_code(404);
            echo 'Конструкция не найдена.';
            return;
        }

        $newType = trim((string) ($_POST['type'] ?? ''));

        $typeChanged = (
            $newType !== ''
            && $newType !== ($structure['type'] ?? '')
        );



        try {
            $service->update(
                $id,
                [
                    'number' => trim((string) ($_POST['number'] ?? '')),
                    'location_description' => trim(
                        (string) ($_POST['location_description'] ?? '')
                    ),
                    'latitude' => $_POST['latitude'] ?? '',
                    'longitude' => $_POST['longitude'] ?? '',
                    'is_visible' => isset($_POST['is_visible']),
                ]
            );
        } catch (\InvalidArgumentException $exception) {
            $error = $exception->getMessage();

            $structure = $service->find($id);
            $structureTypes = $app['config']['structures']['types'];
            $structureStatuses = $app['config']['structures']['statuses'];

            require dirname(__DIR__, 3)
                . '/templates/admin/structures/edit.php';

            return;
        }

        if ($typeChanged) {
            $service->changeType($id, $newType);

            foreach (($structure['surfaces'] ?? []) as $oldSurface) {
                $oldImagePath = $oldSurface['image'] ?? null;

                $imageService->delete(
                    is_string($oldImagePath) ? $oldImagePath : null,
                    $app['config']['paths']['uploads']
                );
            }
        }

        if ($typeChanged) {
            header(
                'Location: /admin/structures/edit?id=' . urlencode($id)
            );
            exit;
        }


        $surfaceImagesToRemove = $_POST['surface_image_remove'] ?? [];

        if (!is_array($surfaceImagesToRemove)) {
            $surfaceImagesToRemove = [];
        }

        foreach ($surfaceImagesToRemove as $surfaceId => $remove) {
            if ($remove !== '1') {
                continue;
            }

            $hasNewImage = isset($_FILES['surface_image']['name'][$surfaceId])
                && $_FILES['surface_image']['name'][$surfaceId] !== '';

            if ($hasNewImage) {
                continue;
            }

            $currentStructure = $service->find($id);

            if ($currentStructure === null) {
                continue;
            }

            foreach (($currentStructure['surfaces'] ?? []) as $surface) {
                if (($surface['id'] ?? '') !== (string) $surfaceId) {
                    continue;
                }

                $oldImagePath = $surface['image'] ?? null;

                $service->updateSurfaceImage(
                    $id,
                    (string) $surfaceId,
                    null
                );

                $imageService->delete(
                    is_string($oldImagePath) ? $oldImagePath : null,
                    $app['config']['paths']['uploads']
                );

                break;
            }
        }

        $surfaceImages = $_FILES['surface_image'] ?? null;

        if (
            is_array($surfaceImages)
            && isset($surfaceImages['name'])
            && is_array($surfaceImages['name'])
        ) {
            foreach ($surfaceImages['name'] as $surfaceId => $fileName) {
                if ($fileName === '') {
                    continue;
                }

                $file = [
                    'name' => $fileName,
                    'type' => $surfaceImages['type'][$surfaceId] ?? '',
                    'tmp_name' => $surfaceImages['tmp_name'][$surfaceId] ?? '',
                    'error' => $surfaceImages['error'][$surfaceId] ?? UPLOAD_ERR_NO_FILE,
                    'size' => $surfaceImages['size'][$surfaceId] ?? 0,
                ];

                $currentStructure = $service->find($id);
                $oldImagePath = null;

                if ($currentStructure !== null) {
                    foreach (($currentStructure['surfaces'] ?? []) as $surface) {
                        if (($surface['id'] ?? '') === (string) $surfaceId) {
                            $oldImagePath = $surface['image'] ?? null;
                            break;
                        }
                    }
                }

                $savedFileName = $imageService->save(
                    $file,
                    $app['config']['paths']['uploads'] . '/structures'
                );

                $service->updateSurfaceImage(
                    $id,
                    (string) $surfaceId,
                    '/uploads/structures/' . $savedFileName
                );

                $imageService->delete(
                    is_string($oldImagePath) ? $oldImagePath : null,
                    $app['config']['paths']['uploads']
                );
            }
        }

        header('Location: /admin/structures');
        exit;
    }

    public function updateSurfaceStatus(array $app): void
    {
        $csrfToken = $_POST['_csrf'] ?? null;

        if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
            http_response_code(419);

            echo 'Недействительный CSRF-токен.';
            return;
        }

        $structureId = trim(
            (string) ($_POST['structure_id'] ?? '')
        );

        $surfaceId = trim(
            (string) ($_POST['surface_id'] ?? '')
        );

        $status = trim(
            (string) ($_POST['status'] ?? '')
        );

        $repository = new StructureRepository(
            $app['config']['paths']['data'] . '/structures.json'
        );

        $service = new StructureService(
            $repository,
            $app['config']['structures']
        );

        $updated = $service->updateSurfaceStatus(
            $structureId,
            $surfaceId,
            $status
        );

        if ($updated === null) {
            http_response_code(404);

            echo 'Конструкция или поверхность не найдена.';
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            ['success' => true],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function updateVisibility(array $app): void
    {
        $csrfToken = $_POST['_csrf'] ?? null;

        if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
            http_response_code(419);

            echo 'Недействительный CSRF-токен.';
            return;
        }

        $structureId = trim(
            (string) ($_POST['structure_id'] ?? '')
        );

        $isVisible = ($_POST['is_visible'] ?? '') === '1';

        $repository = new StructureRepository(
            $app['config']['paths']['data'] . '/structures.json'
        );

        $service = new StructureService(
            $repository,
            $app['config']['structures']
        );

        $structure = $service->find($structureId);

        if ($structure === null) {
            http_response_code(404);

            echo 'Конструкция не найдена.';
            return;
        }

        $updated = $service->update(
            $structureId,
            [
                'number' => (string) ($structure['number'] ?? ''),
                'location_description' => (string) (
                    $structure['location_description'] ?? ''
                ),
                'latitude' => $structure['latitude'] ?? '',
                'longitude' => $structure['longitude'] ?? '',
                'is_visible' => $isVisible,
            ]
        );

        if ($updated === null) {
            http_response_code(404);

            echo 'Конструкция не найдена.';
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            ['success' => true],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function delete(array $app): void
    {
        $csrfToken = $_POST['_csrf'] ?? null;

        if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
            http_response_code(419);

            echo 'Недействительный CSRF-токен.';
            return;
        }

        $id = trim((string) ($_POST['id'] ?? ''));

        $repository = new StructureRepository(
            $app['config']['paths']['data'] . '/structures.json'
        );

        $service = new StructureService(
            $repository,
            $app['config']['structures']
        );

        $structure = $service->find($id);

        if ($structure === null) {
            http_response_code(404);

            echo 'Конструкция не найдена.';
            return;
        }

        $imageService = new StructureImageService();

        foreach (($structure['surfaces'] ?? []) as $surface) {
            $imagePath = $surface['image'] ?? null;

            $imageService->delete(
                is_string($imagePath) ? $imagePath : null,
                $app['config']['paths']['uploads']
            );
        }

        $service->delete($id);

        header('Location: /admin/structures');
        exit;
    }

    public function importForm(array $app): void
    {
        $error = null;
        $success = null;

        require dirname(__DIR__, 3)
            . '/templates/admin/structures/import.php';
    }

    public function import(array $app): void
    {
        $csrfToken = $_POST['_csrf'] ?? null;

        if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
            http_response_code(419);

            echo 'Недействительный CSRF-токен.';
            return;
        }

        $uploadedFile = $_FILES['excel_file'] ?? null;

        if (
            !is_array($uploadedFile)
            || ($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            || empty($uploadedFile['tmp_name'])
        ) {
            $error = 'Не удалось загрузить Excel-файл.';
            $success = null;

            require dirname(__DIR__, 3)
                . '/templates/admin/structures/import.php';

            return;
        }

        $originalName = (string) ($uploadedFile['name'] ?? '');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if ($extension !== 'xlsx') {
            $error = 'Допускается только Excel-файл формата .xlsx.';
            $success = null;

            require dirname(__DIR__, 3)
                . '/templates/admin/structures/import.php';

            return;
        }

        $fileSize = (int) ($uploadedFile['size'] ?? 0);

        if ($fileSize <= 0 || $fileSize > 5 * 1024 * 1024) {
            $error = 'Размер Excel-файла не должен превышать 5 МБ.';
            $success = null;

            require dirname(__DIR__, 3)
                . '/templates/admin/structures/import.php';

            return;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file((string) $uploadedFile['tmp_name']);

        $allowedMimeTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
        ];

        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            $error = 'Загруженный файл не является корректным Excel-файлом .xlsx.';
            $success = null;

            require dirname(__DIR__, 3)
                . '/templates/admin/structures/import.php';

            return;
        }

        try {
            $importService = new StructureImportService(
                $app['config']['structures']
            );

            $structuresFile = dirname(__DIR__, 3)
                . '/data/structures.json';

            $backupDirectory = dirname(__DIR__, 3)
                . '/data/backups';

            $count = $importService->import(
                (string) $uploadedFile['tmp_name'],
                $structuresFile,
                $backupDirectory
            );

            $uploadsDirectory = rtrim(
                (string) $app['config']['paths']['uploads'],
                '/\\'
            ) . '/structures';

            if (is_dir($uploadsDirectory)) {
                $files = glob($uploadsDirectory . '/*');

                if ($files !== false) {
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            @unlink($file);
                        }
                    }
                }
            }

            $error = null;
            $success = 'Импорт успешно завершён. Загружено конструкций: '
                . $count . '.';
        } catch (\Throwable $exception) {
            $error = $exception->getMessage();
            $success = null;
        }

        require dirname(__DIR__, 3)
            . '/templates/admin/structures/import.php';
        }

        private function getStructureTypeName(array $app, string $type): string
        {
            $typeConfig = $app['config']['structures']['types'][$type] ?? null;

            if (!is_array($typeConfig) || empty($typeConfig['name'])) {
                throw new \RuntimeException(
                    'Неизвестный тип конструкции: ' . $type
                );
            }

            return (string) $typeConfig['name'];
        }

        public function export(array $app): void
        {
            $repository = new StructureRepository(
                $app['config']['paths']['data'] . '/structures.json'
            );

            $structures = $repository->all(false);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setTitle('Конструкции');

            $sheet->fromArray(
                [
                    'Широта',
                    'Долгота',
                    'Описание',
                    'Номер метки',
                    'Тип конструкции',
                ],
                null,
                'A1'
            );

            $row = 2;

            foreach ($structures as $structure) {
                $type = (string) ($structure['type'] ?? '');

                $sheet->setCellValue(
                    'A' . $row,
                    $structure['latitude'] ?? null
                );

                $sheet->setCellValue(
                    'B' . $row,
                    $structure['longitude'] ?? null
                );

                $sheet->setCellValue(
                    'C' . $row,
                    (string) ($structure['location_description'] ?? '')
                );

                $sheet->setCellValueExplicit(
                    'D' . $row,
                    (string) ($structure['number'] ?? ''),
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );

                $sheet->setCellValue(
                    'E' . $row,
                    $this->getStructureTypeName($app, $type)
                );

                $row++;
            }

            $filename = 'structures-' . date('Y-m-d-His') . '.xlsx';

            header(
                'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            );
            header(
                'Content-Disposition: attachment; filename="' . $filename . '"'
            );
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

            $spreadsheet->disconnectWorksheets();

            exit;
        }
}