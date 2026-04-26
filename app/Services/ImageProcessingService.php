<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GD;

class ImageProcessingService
{
    /**
     * Безопасная загрузка и обработка изображения
     */
    public function processAndSave(UploadedFile $file, array $options = []): array
    {
        // 1. Проверка на реальное изображение (защита от загрузки вредоносных файлов)
        $this->validateImageFile($file);

        // 2. Получаем информацию об изображении
        $imageInfo = getimagesize($file->getRealPath());
        if (!$imageInfo) {
            throw new \Exception('Некорректный файл изображения');
        }

        $mimeType = $imageInfo['mime'];
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // 3. Создаем ресурс GD
        $gdResource = $this->createGdResource($file, $mimeType);

        // 4. Оптимизируем изображение
        $gdResource = $this->optimizeImage($gdResource, $mimeType, $width, $height);

        // 5. Создаем уникальное имя файла
        $fileName = $this->generateSafeFileName($file);
        $filePath = 'photos/' . $fileName;

        // 6. Сохраняем оптимизированное изображение
        Storage::disk('public')->put($filePath, $this->getImageBlob($gdResource, $mimeType));

        // 7. Создаем дополнительные варианты (если нужно)
        $variants = [];
        if (isset($options['create_variants']) && $options['create_variants']) {
            $variants = $this->createVariants($gdResource, $mimeType, $fileName);
        }

        // 8. Освобождаем память
        imagedestroy($gdResource);

        return [
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => Storage::disk('public')->size($filePath),
            'mime_type' => $mimeType,
            'width' => $width,
            'height' => $height,
            'variants' => $variants
        ];
    }

    /**
     * Получение GD ресурса
     */
    private function createGdResource(UploadedFile $file, string $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($file->getRealPath());
            case 'image/png':
                $resource = imagecreatefrompng($file->getRealPath());
                // Сохраняем прозрачность
                imagealphablending($resource, false);
                imagesavealpha($resource, true);
                return $resource;
            case 'image/gif':
                return imagecreatefromgif($file->getRealPath());
            case 'image/webp':
                return imagecreatefromwebp($file->getRealPath());
            default:
                throw new \Exception('Неподдерживаемый формат изображения');
        }
    }

    /**
     * Оптимизация изображения
     */
    private function optimizeImage($gdResource, string $mimeType, int $width, int $height)
    {
        // Если изображение слишком большое, уменьшаем его
        $maxWidth = 1920;
        $maxHeight = 1080;

        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = (int)($width * $ratio);
            $newHeight = (int)($height * $ratio);

            $newImage = imagecreatetruecolor($newWidth, $newHeight);

            // Сохраняем прозрачность для PNG
            if ($mimeType === 'image/png') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
            }

            imagecopyresampled($newImage, $gdResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($gdResource);

            return $newImage;
        }

        return $gdResource;
    }

    /**
     * Конвертация в другой формат
     */
    public function convertFormat($gdResource, string $fromMime, string $toFormat): string
    {
        switch ($toFormat) {
            case 'jpeg':
            case 'jpg':
                ob_start();
                imagejpeg($gdResource, null, 85);
                return ob_get_clean();
            case 'png':
                ob_start();
                imagepng($gdResource, null, 8);
                return ob_get_clean();
            case 'webp':
                ob_start();
                imagewebp($gdResource, null, 80);
                return ob_get_clean();
            case 'gif':
                ob_start();
                imagegif($gdResource);
                return ob_get_clean();
            default:
                throw new \Exception('Неподдерживаемый формат для конвертации');
        }
    }

    /**
     * Изменение размера с сохранением пропорций
     */
    public function resize($gdResource, int $targetWidth, int $targetHeight, bool $crop = false): object
    {
        $originalWidth = imagesx($gdResource);
        $originalHeight = imagesy($gdResource);

        if ($crop) {
            // Кроп под заданное соотношение
            $targetRatio = $targetWidth / $targetHeight;
            $originalRatio = $originalWidth / $originalHeight;

            if ($originalRatio > $targetRatio) {
                $cropWidth = $originalHeight * $targetRatio;
                $cropHeight = $originalHeight;
                $cropX = ($originalWidth - $cropWidth) / 2;
                $cropY = 0;
            } else {
                $cropWidth = $originalWidth;
                $cropHeight = $originalWidth / $targetRatio;
                $cropX = 0;
                $cropY = ($originalHeight - $cropHeight) / 2;
            }

            $cropped = imagecreatetruecolor($targetWidth, $targetHeight);

            // Сохраняем прозрачность
            $this->preserveTransparency($cropped, $gdResource);

            imagecopyresampled($cropped, $gdResource, 0, 0, (int)$cropX, (int)$cropY, $targetWidth, $targetHeight, (int)$cropWidth, (int)$cropHeight);

            return (object)[
                'resource' => $cropped,
                'width' => $targetWidth,
                'height' => $targetHeight
            ];
        } else {
            // Ресайз с сохранением пропорций
            $ratio = min($targetWidth / $originalWidth, $targetHeight / $originalHeight);
            $newWidth = max(1, (int)($originalWidth * $ratio));
            $newHeight = max(1, (int)($originalHeight * $ratio));

            $resized = imagecreatetruecolor($newWidth, $newHeight);

            // Сохраняем прозрачность
            $this->preserveTransparency($resized, $gdResource);

            imagecopyresampled($resized, $gdResource, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            return (object)[
                'resource' => $resized,
                'width' => $newWidth,
                'height' => $newHeight
            ];
        }
    }

    /**
     * Сохранение прозрачности для изображений
     */
    private function preserveTransparency($newImage, $gdResource): void
    {
        // Проверяем, есть ли прозрачность в исходном изображении
        $hasTransparency = false;

        // Для PNG изображений сохраняем прозрачность
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);

        // Заполняем прозрачным цветом
        $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefilledrectangle($newImage, 0, 0, imagesx($newImage), imagesy($newImage), $transparent);
    }

    /**
     * Создание вариантов изображения (превью, среднее, большое)
     */
    private function createVariants($gdResource, string $mimeType, string $originalFileName): array
    {
        $variants = [];
        $sizes = [
            'thumb' => ['width' => 150, 'height' => 150, 'crop' => true],
            'medium' => ['width' => 500, 'height' => 500, 'crop' => false],
            'large' => ['width' => 1024, 'height' => 1024, 'crop' => false]
        ];

        foreach ($sizes as $key => $size) {
            $resized = $this->resize($gdResource, $size['width'], $size['height'], $size['crop']);

            $variantName = $key . '_' . $originalFileName;
            $variantPath = 'photos/variants/' . $variantName;

            $blob = $this->getImageBlob($resized->resource, $mimeType);
            Storage::disk('public')->put($variantPath, $blob);

            $variants[$key] = $variantPath;

            imagedestroy($resized->resource);
        }

        return $variants;
    }

    /**
     * Получение blob изображения
     */
    private function getImageBlob($gdResource, string $mimeType): string
    {
        ob_start();
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($gdResource, null, 85);
                break;
            case 'image/png':
                imagepng($gdResource, null, 8);
                break;
            case 'image/gif':
                imagegif($gdResource);
                break;
            case 'image/webp':
                imagewebp($gdResource, null, 80);
                break;
            default:
                imagejpeg($gdResource, null, 85);
        }
        return ob_get_clean();
    }

    /**
     * Валидация изображения на безопасность
     */
    private function validateImageFile(UploadedFile $file): void
    {
        // Проверка MIME типа
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Неподдерживаемый формат файла');
        }

        // Проверка на вредоносный код (читаем первые 100 байт)
        $handle = fopen($file->getRealPath(), 'rb');
        $firstBytes = fread($handle, 100);
        fclose($handle);

        // Проверка на PHP код в изображении
        if (preg_match('/<\?php/i', $firstBytes)) {
            throw new \Exception('Обнаружен подозрительный код в изображении');
        }

        // Проверка реального содержимого через GD
        if (!@getimagesize($file->getRealPath())) {
            throw new \Exception('Некорректное изображение');
        }
    }

    /**
     * Генерация безопасного имени файла
     */
    private function generateSafeFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $safeExtension = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $extension));

        return time() . '_' . bin2hex(random_bytes(16)) . '.' . $safeExtension;
    }

    /**
     * Удаление изображения и всех его вариантов
     */
    public function deleteAllVersions($photo): void
    {
        // Удаляем оригинал
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        // Удаляем оптимизированную версию (если есть)
        if (isset($photo->optimized_path) && $photo->optimized_path && Storage::disk('public')->exists($photo->optimized_path)) {
            Storage::disk('public')->delete($photo->optimized_path);
        }

        // Удаляем варианты
        if (isset($photo->variants) && $photo->variants && is_array($photo->variants)) {
            foreach ($photo->variants as $variant) {
                if (Storage::disk('public')->exists($variant)) {
                    Storage::disk('public')->delete($variant);
                }
            }
        }
    }
}
