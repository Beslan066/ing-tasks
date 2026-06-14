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
     * Получение GD ресурса из пути к файлу
     */
    public function createGdResourceFromPath(string $path, string $mimeType)
    {
        if (!file_exists($path)) {
            throw new \Exception('Файл не найден: ' . $path);
        }

        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                $resource = imagecreatefrompng($path);
                imagealphablending($resource, false);
                imagesavealpha($resource, true);
                return $resource;
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
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
     * Конвертация в другой формат с возможностью указания качества
     */
    public function convertFormat($gdResource, string $fromMime, string $toFormat, int $quality = 85): string
    {
        // Для PNG качество игнорируется, так как используется сжатие без потерь
        $quality = max(1, min(100, $quality));

        switch ($toFormat) {
            case 'jpeg':
            case 'jpg':
                ob_start();
                imagejpeg($gdResource, null, $quality);
                return ob_get_clean();
            case 'png':
                // Для PNG качество преобразуется в уровень сжатия (0-9)
                $compression = 9 - (int)($quality / 11.11); // 85 качество -> ~1-2 уровень сжатия
                $compression = max(0, min(9, $compression));
                ob_start();
                imagepng($gdResource, null, $compression);
                return ob_get_clean();
            case 'webp':
                ob_start();
                imagewebp($gdResource, null, $quality);
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
     * Конвертация изображения с проверкой MIME типа
     */
    public function convertImageWithMimeCheck($gdResource, string $originalMime, string $targetFormat, int $quality = 85): string
    {
        // Если формат совпадает, просто возвращаем изображение в исходном формате
        $originalFormat = $this->getFormatFromMime($originalMime);
        if ($originalFormat === $targetFormat) {
            return $this->getImageBlob($gdResource, $originalMime, $quality);
        }

        return $this->convertFormat($gdResource, $originalMime, $targetFormat, $quality);
    }

    /**
     * Получение формата из MIME типа
     */
    private function getFormatFromMime(string $mimeType): string
    {
        $map = [
            'image/jpeg' => 'jpeg',
            'image/jpg' => 'jpeg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];

        return $map[$mimeType] ?? 'jpeg';
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
     * Изменение размера с автоматическим вычислением недостающих параметров
     */
    public function resizeAuto($gdResource, ?int $targetWidth, ?int $targetHeight, bool $crop = false, bool $keepProportions = true): object
    {
        $originalWidth = imagesx($gdResource);
        $originalHeight = imagesy($gdResource);

        // Если оба параметра не указаны, возвращаем оригинал
        if (!$targetWidth && !$targetHeight) {
            return (object)[
                'resource' => $gdResource,
                'width' => $originalWidth,
                'height' => $originalHeight
            ];
        }

        // Если указан только один параметр, вычисляем второй с сохранением пропорций
        if ($keepProportions && (!$targetWidth || !$targetHeight)) {
            $ratio = $originalWidth / $originalHeight;

            if ($targetWidth && !$targetHeight) {
                $targetHeight = (int)($targetWidth / $ratio);
            } elseif (!$targetWidth && $targetHeight) {
                $targetWidth = (int)($targetHeight * $ratio);
            }
        }

        // Если все еще нет значений, используем оригинальные
        $targetWidth = $targetWidth ?? $originalWidth;
        $targetHeight = $targetHeight ?? $originalHeight;

        return $this->resize($gdResource, $targetWidth, $targetHeight, $crop);
    }

    /**
     * Сохранение прозрачности для изображений
     */
    private function preserveTransparency($newImage, $gdResource): void
    {
        // Проверяем, есть ли прозрачность в исходном изображении
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
     * Получение blob изображения с учетом качества
     */
    private function getImageBlob($gdResource, string $mimeType, int $quality = 85): string
    {
        $quality = max(1, min(100, $quality));

        ob_start();
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($gdResource, null, $quality);
                break;
            case 'image/png':
                // Для PNG качество преобразуется в уровень сжатия
                $compression = 9 - (int)($quality / 11.11);
                $compression = max(0, min(9, $compression));
                imagepng($gdResource, null, $compression);
                break;
            case 'image/gif':
                imagegif($gdResource);
                break;
            case 'image/webp':
                imagewebp($gdResource, null, $quality);
                break;
            default:
                imagejpeg($gdResource, null, $quality);
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

    /**
     * Получение информации об изображении по пути
     */
    public function getImageInfo(string $path): array
    {
        $fullPath = Storage::disk('public')->path($path);

        if (!file_exists($fullPath)) {
            throw new \Exception('Файл не найден');
        }

        $info = getimagesize($fullPath);
        if (!$info) {
            throw new \Exception('Не удалось получить информацию об изображении');
        }

        return [
            'width' => $info[0],
            'height' => $info[1],
            'mime' => $info['mime'],
            'size' => filesize($fullPath)
        ];
    }

    /**
     * Применение всех операций к изображению (цепочка преобразований)
     */
    public function applyOperations(string $filePath, array $operations = []): string
    {
        $fullPath = Storage::disk('public')->path($filePath);

        if (!file_exists($fullPath)) {
            throw new \Exception('Файл не найден');
        }

        $mimeType = mime_content_type($fullPath);
        $gdResource = $this->createGdResourceFromPath($fullPath, $mimeType);

        // Применяем соотношение сторон
        if (isset($operations['ratio'])) {
            $ratios = [
                '16:9' => 16/9,
                '4:3' => 4/3,
                '1:1' => 1,
                '3:2' => 1.5,
                '2:3' => 0.6667
            ];

            $targetRatio = $ratios[$operations['ratio']] ?? null;
            if ($targetRatio) {
                $currentWidth = imagesx($gdResource);
                $currentHeight = imagesy($gdResource);
                $currentRatio = $currentWidth / $currentHeight;

                if ($currentRatio > $targetRatio) {
                    $newWidth = (int)($currentHeight * $targetRatio);
                    $cropX = (int)(($currentWidth - $newWidth) / 2);
                    $result = $this->resize($gdResource, $newWidth, $currentHeight, true);
                    imagedestroy($gdResource);
                    $gdResource = $result->resource;
                } else {
                    $newHeight = (int)($currentWidth / $targetRatio);
                    $result = $this->resize($gdResource, $currentWidth, $newHeight, true);
                    imagedestroy($gdResource);
                    $gdResource = $result->resource;
                }
            }
        }

        // Применяем изменение размера
        if (isset($operations['resize'])) {
            $resize = $operations['resize'];
            $result = $this->resizeAuto(
                $gdResource,
                $resize['width'] ?? null,
                $resize['height'] ?? null,
                $resize['crop'] ?? false,
                $resize['keep_proportions'] ?? true
            );
            imagedestroy($gdResource);
            $gdResource = $result->resource;
        }

        // Применяем конвертацию
        $format = $operations['format'] ?? $this->getFormatFromMime($mimeType);
        $quality = $operations['quality'] ?? 85;

        $blob = $this->convertFormat($gdResource, $mimeType, $format, $quality);
        imagedestroy($gdResource);

        // Сохраняем временный файл
        $tempFileName = 'temp_' . bin2hex(random_bytes(8)) . '.' . ($format === 'jpeg' ? 'jpg' : $format);
        $tempPath = 'photos/temp/' . $tempFileName;
        Storage::disk('public')->put($tempPath, $blob);

        return $tempPath;
    }

    /**
     * Получение blob изображения с указанным качеством
     */
    public function getImageBlobWithQuality($gdResource, string $mimeType, int $quality = 85): string
    {
        $quality = max(1, min(100, $quality));

        ob_start();
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($gdResource, null, $quality);
                break;
            case 'image/png':
                $compression = 9 - (int)($quality / 11.11);
                $compression = max(0, min(9, $compression));
                imagepng($gdResource, null, $compression);
                break;
            case 'image/webp':
                imagewebp($gdResource, null, $quality);
                break;
            default:
                imagejpeg($gdResource, null, $quality);
        }
        return ob_get_clean();
    }
}
