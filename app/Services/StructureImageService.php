<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

final class StructureImageService
{
    public function validate(array $file): void
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($error === UPLOAD_ERR_NO_FILE) {
            throw new InvalidArgumentException(
                'Фотография не выбрана.'
            );
        }

        if ($error !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException(
                'Ошибка загрузки изображения.'
            );
        }

        $maxFileSize = 10 * 1024 * 1024;

        if (($file['size'] ?? 0) > $maxFileSize) {
            throw new InvalidArgumentException(
                'Размер изображения не должен превышать 10 МБ.'
            );
        }

        if (($file['size'] ?? 0) > $maxFileSize) {
            throw new InvalidArgumentException(
                'Размер изображения не должен превышать 10 МБ.'
            );
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');

        $imageInfo = getimagesize($tmpName);

        if ($imageInfo === false) {
            throw new InvalidArgumentException(
                'Не удалось определить параметры изображения.'
            );
        }

        [$width, $height] = $imageInfo;

        if ($width > 6000 || $height > 6000) {
            throw new InvalidArgumentException(
                'Размер изображения не должен превышать 6000×6000 пикселей.'
            );
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpName);

        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        if (!in_array($mimeType, $allowedTypes, true)) {
            throw new InvalidArgumentException(
                'Допустимы только JPG, PNG и WebP.'
            );
        }
    }

    public function save(array $file, string $uploadDir): string
    {
        $this->validate($file);

        $tmpName = (string) $file['tmp_name'];

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpName);

        $fileName = bin2hex(random_bytes(8)) . '.webp';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $destination = $uploadDir . '/' . $fileName;

        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($tmpName);
                break;

            case 'image/png':
                $sourceImage = imagecreatefrompng($tmpName);
                break;

            case 'image/webp':
                $sourceImage = imagecreatefromwebp($tmpName);
                break;

            default:
                $sourceImage = false;
                break;
        }

        if ($sourceImage === false) {
            throw new InvalidArgumentException(
                'Не удалось обработать изображение.'
            );
        }

        $maxWidth = 2000;
        $maxHeight = 2000;

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        if ($sourceWidth > $maxWidth || $sourceHeight > $maxHeight) {
            $scale = min(
                $maxWidth / $sourceWidth,
                $maxHeight / $sourceHeight
            );

            $targetWidth = (int) round($sourceWidth * $scale);
            $targetHeight = (int) round($sourceHeight * $scale);

            $resizedImage = imagecreatetruecolor(
                $targetWidth,
                $targetHeight
            );

            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);

            $transparent = imagecolorallocatealpha(
                $resizedImage,
                0,
                0,
                0,
                127
            );

            imagefilledrectangle(
                $resizedImage,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $transparent
            );

            imagecopyresampled(
                $resizedImage,
                $sourceImage,
                0,
                0,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $sourceWidth,
                $sourceHeight
            );

            imagedestroy($sourceImage);

            $sourceImage = $resizedImage;
        }

        if (!imagewebp($sourceImage, $destination, 82)) {
            imagedestroy($sourceImage);

            throw new \RuntimeException(
                'Не удалось сохранить изображение.'
            );
        }

        imagedestroy($sourceImage);

        return $fileName;
    }

    public function delete(?string $imagePath, string $uploadsDir): void
    {
        if ($imagePath === null || $imagePath === '') {
            return;
        }

        if (!str_starts_with($imagePath, '/uploads/structures/')) {
            return;
        }

        $filePath = $uploadsDir
            . str_replace('/uploads', '', $imagePath);

        if (is_file($filePath)) {
            unlink($filePath);
        }
    }
}