<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\PhotoCategory;
use App\Models\Tag;
use App\Services\ImageProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PhotobankController extends Controller
{
    protected $imageProcessor;

    public function __construct(ImageProcessingService $imageProcessor)
    {
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * Получение GD ресурса из загруженного файла
     */
    private function createGdResourceFromUploadedFile($file, string $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($file->getRealPath());
            case 'image/png':
                $resource = imagecreatefrompng($file->getRealPath());
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

    public function index(Request $request)
    {
        // Для первоначальной загрузки страницы
        if (!$request->ajax()) {
            $categories = PhotoCategory::orderBy('name')->get();
            $tags = Tag::orderBy('name')->get();

            // ОТЛАДКА - проверяем в логах
            \Log::info('=== INDEX METHOD DEBUG ===');
            \Log::info('Categories count: ' . $categories->count());
            \Log::info('Tags count: ' . $tags->count());

            if ($categories->count() > 0) {
                \Log::info('First category: ' . $categories->first()->name);
            }

            // ПЕРЕДАЕМ ДАННЫЕ ЧЕРЕЗ with()
            return view('frontend.photobank.index')
                ->with('categories', $categories)
                ->with('tags', $tags);
        }

        // Асинхронная загрузка фотографий (только одобренные для публичного просмотра)
        $query = Photo::with(['category', 'tags', 'user']);

        // Поиск
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('tags', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Фильтрация по категории
        if ($request->has('category') && $request->category && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        // Фильтрация по тегам
        if ($request->has('tags') && $request->tags && $request->tags !== 'all') {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('tags.id', $request->tags);
            });
        }

        $photos = $query->latest()->paginate(20);

        // Формируем JSON ответ с полными данными
        $photosData = $photos->map(function($photo) {
            return [
                'id' => $photo->id,
                'title' => $photo->title,
                'description' => $photo->description,
                'file_path' => $photo->file_path,
                'url' => $photo->file_path ? Storage::url($photo->file_path) : null,
                'preview_url' => $photo->getPreviewUrl(),
                'medium_url' => $photo->getMediumUrl(),
                'width' => $photo->width,
                'height' => $photo->height,
                'file_size' => $photo->file_size_formatted,
                'category' => $photo->category ? [
                    'id' => $photo->category->id,
                    'name' => $photo->category->name
                ] : null,
                'tags' => $photo->tags->map(function($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name
                    ];
                }),
                'user' => $photo->user ? [
                    'id' => $photo->user->id,
                    'name' => $photo->user->name
                ] : null,
                'created_at' => $photo->created_at->toDateTimeString(),
                'is_owner' => auth()->check() ? auth()->id() === $photo->user_id : false
            ];
        });

        return response()->json([
            'success' => true,
            'photos' => $photosData,
            'next_page_url' => $photos->nextPageUrl(),
            'prev_page_url' => $photos->previousPageUrl(),
            'total' => $photos->total(),
            'current_page' => $photos->currentPage(),
            'last_page' => $photos->lastPage(),
            'per_page' => $photos->perPage()
        ]);
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:photo_categories,name'
        ]);

        $category = PhotoCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Категория создана успешно'
        ]);
    }

    public function createTag(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name'
        ]);

        $tag = Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return response()->json([
            'success' => true,
            'tag' => $tag,
            'message' => 'Тег создан успешно'
        ]);
    }

    public function storePhoto(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'category_id' => 'required|exists:photo_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        DB::beginTransaction();

        try {
            $image = $request->file('photo');

            // Обработка изображения через сервис
            $processed = $this->imageProcessor->processAndSave($image, ['create_variants' => true]);

            // Создаем запись в базе данных
            $photo = Photo::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $processed['file_path'],
                'file_name' => $processed['file_name'],
                'file_size' => $processed['file_size'],
                'mime_type' => $processed['mime_type'],
                'width' => $processed['width'],
                'height' => $processed['height'],
                'variants' => $processed['variants'],
                'category_id' => $request->category_id,
                'user_id' => auth()->id(),
                'is_approved' => false, // На модерацию
                'metadata' => [
                    'original_name' => $image->getClientOriginalName(),
                    'original_extension' => $image->getClientOriginalExtension(),
                    'uploaded_at' => now()->toDateTimeString(),
                    'ip_address' => $request->ip()
                ]
            ]);

            // Привязываем теги
            if ($request->has('tags') && !empty($request->tags)) {
                $photo->tags()->sync($request->tags);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Фотография успешно загружена и отправлена на модерацию.',
                'photo' => $photo->load(['category', 'tags'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Ошибка загрузки фото: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке фотографии: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCategories()
    {
        $categories = PhotoCategory::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function getTags()
    {
        $tags = Tag::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $tags
        ]);
    }

    /**
     * Конвертация изображения в другой формат
     */
    public function convertImage(Request $request, Photo $photo)
    {
        // Проверка прав
        if (auth()->id() !== $photo->user_id && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для конвертации этого фото'
            ], 403);
        }

        $request->validate([
            'format' => 'required|in:jpeg,png,webp,gif',
            'quality' => 'sometimes|integer|min:1|max:100'
        ]);

        try {
            $filePath = Storage::disk('public')->path($photo->file_path);

            if (!file_exists($filePath)) {
                throw new \Exception('Файл изображения не найден: ' . $filePath);
            }

            $quality = $request->input('quality', 85);
            $gdResource = $this->createGdResourceFromPath($filePath, $photo->mime_type);

            $targetFormat = $request->format;

            // Создаем директорию если не существует
            $convertDir = storage_path('app/public/photos/converted');
            if (!file_exists($convertDir)) {
                mkdir($convertDir, 0755, true);
            }

            $newFileName = pathinfo($photo->file_name, PATHINFO_FILENAME) . '_converted.' . ($targetFormat === 'jpeg' ? 'jpg' : $targetFormat);
            $newPath = 'photos/converted/' . $newFileName;

            // Получаем blob в нужном формате
            $blob = $this->getImageBlobWithFormat($gdResource, $targetFormat, $quality);
            Storage::disk('public')->put($newPath, $blob);

            imagedestroy($gdResource);

            return response()->json([
                'success' => true,
                'message' => 'Изображение успешно конвертировано',
                'url' => Storage::url($newPath),
                'path' => $newPath,
                'format' => $targetFormat
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка конвертации: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка конвертации: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Создание GD ресурса из пути к файлу
     */
    private function createGdResourceFromPath(string $path, string $mimeType)
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
                throw new \Exception('Неподдерживаемый формат изображения: ' . $mimeType);
        }
    }

    /**
     * Получение blob изображения из GD ресурса
     */
    private function getImageBlobFromResource($gdResource, string $mimeType, int $quality = 85): string
    {
        ob_start();
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($gdResource, null, $quality);
                break;
            case 'image/png':
                // Для PNG качество преобразуется в уровень сжатия (0-9)
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
     * Получение blob изображения с конвертацией в указанный формат
     */
    private function getImageBlobWithFormat($gdResource, string $targetFormat, int $quality = 85): string
    {
        $quality = max(1, min(100, $quality));

        ob_start();
        switch ($targetFormat) {
            case 'jpeg':
                imagejpeg($gdResource, null, $quality);
                break;
            case 'png':
                $compression = 9 - (int)($quality / 11.11);
                $compression = max(0, min(9, $compression));
                imagepng($gdResource, null, $compression);
                break;
            case 'webp':
                imagewebp($gdResource, null, $quality);
                break;
            case 'gif':
                imagegif($gdResource);
                break;
            default:
                imagejpeg($gdResource, null, $quality);
        }
        return ob_get_clean();
    }

    /**
     * Изменение размера изображения
     */
    public function resizeImage(Request $request, Photo $photo)
    {
        // Проверка прав
        if (auth()->id() !== $photo->user_id && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для изменения этого фото'
            ], 403);
        }

        $request->validate([
            'width' => 'nullable|integer|min:1|max:4000',
            'height' => 'nullable|integer|min:1|max:4000',
            'crop' => 'boolean',
            'keep_proportions' => 'boolean'
        ]);

        try {
            $filePath = Storage::disk('public')->path($photo->file_path);

            if (!file_exists($filePath)) {
                throw new \Exception('Файл изображения не найден: ' . $filePath);
            }

            $gdResource = $this->createGdResourceFromPath($filePath, $photo->mime_type);

            $originalWidth = imagesx($gdResource);
            $originalHeight = imagesy($gdResource);

            $width = $request->input('width');
            $height = $request->input('height');
            $crop = $request->input('crop', false);
            $keepProportions = $request->input('keep_proportions', true);

            // Если указан только один параметр, вычисляем второй с сохранением пропорций
            if ($keepProportions) {
                $ratio = $originalWidth / $originalHeight;

                if ($width && !$height) {
                    $height = (int)($width / $ratio);
                } elseif (!$width && $height) {
                    $width = (int)($height * $ratio);
                }
            }

            // Если оба параметра не указаны, используем оригинальные размеры
            if (!$width && !$height) {
                $width = $originalWidth;
                $height = $originalHeight;
            }

            // Применяем ресайз
            if ($crop) {
                // Кроп под заданные размеры
                $targetRatio = $width / $height;
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

                $resized = imagecreatetruecolor($width, $height);

                if ($photo->mime_type === 'image/png') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                    imagefilledrectangle($resized, 0, 0, $width, $height, $transparent);
                }

                imagecopyresampled($resized, $gdResource, 0, 0, (int)$cropX, (int)$cropY, $width, $height, (int)$cropWidth, (int)$cropHeight);
                $newWidth = $width;
                $newHeight = $height;
            } else {
                // Ресайз с сохранением пропорций
                $ratio = min($width / $originalWidth, $height / $originalHeight);
                $newWidth = (int)($originalWidth * $ratio);
                $newHeight = (int)($originalHeight * $ratio);

                $resized = imagecreatetruecolor($newWidth, $newHeight);

                if ($photo->mime_type === 'image/png') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                    imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
                }

                imagecopyresampled($resized, $gdResource, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            }

            // Создаем директорию если не существует
            $resizeDir = storage_path('app/public/photos/resized');
            if (!file_exists($resizeDir)) {
                mkdir($resizeDir, 0755, true);
            }

            $extension = pathinfo($photo->file_path, PATHINFO_EXTENSION);
            $newFileName = pathinfo($photo->file_name, PATHINFO_FILENAME) .
                "_{$newWidth}x{$newHeight}." . $extension;
            $newPath = 'photos/resized/' . $newFileName;

            $blob = $this->getImageBlobFromResource($resized, $photo->mime_type);
            Storage::disk('public')->put($newPath, $blob);

            imagedestroy($gdResource);
            imagedestroy($resized);

            return response()->json([
                'success' => true,
                'message' => 'Изображение успешно изменено',
                'url' => Storage::url($newPath),
                'path' => $newPath,
                'width' => $newWidth,
                'height' => $newHeight
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка изменения размера: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка изменения размера: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Изменение соотношения сторон
     */
    public function changeAspectRatio(Request $request, Photo $photo)
    {
        // Проверка прав
        if (auth()->id() !== $photo->user_id && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для изменения этого фото'
            ], 403);
        }

        $request->validate([
            'ratio' => 'required|string|in:16:9,4:3,1:1,3:2,2:3'
        ]);

        $ratios = [
            '16:9' => 16/9,
            '4:3' => 4/3,
            '1:1' => 1,
            '3:2' => 1.5,
            '2:3' => 0.6667
        ];

        $targetRatio = $ratios[$request->ratio];

        try {
            // Получаем полный путь к файлу
            $filePath = Storage::disk('public')->path($photo->file_path);

            if (!file_exists($filePath)) {
                throw new \Exception('Файл изображения не найден: ' . $filePath);
            }

            // Создаем ресурс GD в зависимости от типа файла
            $gdResource = $this->createGdResourceFromPath($filePath, $photo->mime_type);

            $originalWidth = imagesx($gdResource);
            $originalHeight = imagesy($gdResource);
            $originalRatio = $originalWidth / $originalHeight;

            if ($originalRatio > $targetRatio) {
                // Обрезаем по ширине
                $newWidth = (int)($originalHeight * $targetRatio);
                $newHeight = $originalHeight;
                $cropX = (int)(($originalWidth - $newWidth) / 2);
                $cropY = 0;
            } else {
                // Обрезаем по высоте
                $newWidth = $originalWidth;
                $newHeight = (int)($originalWidth / $targetRatio);
                $cropX = 0;
                $cropY = (int)(($originalHeight - $newHeight) / 2);
            }

            $cropped = imagecreatetruecolor($newWidth, $newHeight);

            // Сохраняем прозрачность для PNG
            if ($photo->mime_type === 'image/png') {
                imagealphablending($cropped, false);
                imagesavealpha($cropped, true);
                $transparent = imagecolorallocatealpha($cropped, 0, 0, 0, 127);
                imagefilledrectangle($cropped, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled($cropped, $gdResource, 0, 0, $cropX, $cropY, $newWidth, $newHeight, $newWidth, $newHeight);

            // Создаем директорию если не существует
            $ratioDir = storage_path('app/public/photos/ratio');
            if (!file_exists($ratioDir)) {
                mkdir($ratioDir, 0755, true);
            }

            $extension = pathinfo($photo->file_path, PATHINFO_EXTENSION);
            $newFileName = pathinfo($photo->file_name, PATHINFO_FILENAME) .
                "_ratio_" . str_replace(':', 'x', $request->ratio) . "." . $extension;
            $newPath = 'photos/ratio/' . $newFileName;

            // Сохраняем изображение
            $blob = $this->getImageBlobFromResource($cropped, $photo->mime_type);
            Storage::disk('public')->put($newPath, $blob);

            imagedestroy($gdResource);
            imagedestroy($cropped);

            return response()->json([
                'success' => true,
                'message' => 'Соотношение сторон успешно изменено',
                'url' => Storage::url($newPath),
                'path' => $newPath,
                'width' => $newWidth,
                'height' => $newHeight,
                'ratio' => $request->ratio
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка изменения соотношения: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка изменения соотношения: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удаление фото
     */
    public function destroy(Photo $photo)
    {
        // Проверка прав
        if (auth()->id() !== $photo->user_id && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав на удаление этого фото'
            ], 403);
        }

        DB::beginTransaction();

        try {
            // Удаляем файлы
            $this->imageProcessor->deleteAllVersions($photo);

            // Удаляем связи с тегами
            $photo->tags()->detach();

            // Удаляем запись из БД
            $photo->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Фотография успешно удалена'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Ошибка удаления фото: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении фотографии: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Восстановление фото
     */
    public function restore($id)
    {
        $photo = Photo::withTrashed()->findOrFail($id);

        // Проверка прав
        if (auth()->id() !== $photo->user_id && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав на восстановление этого фото'
            ], 403);
        }

        $photo->restore();

        return response()->json([
            'success' => true,
            'message' => 'Фотография успешно восстановлена'
        ]);
    }

    /**
     * Полное удаление фото
     */
    public function forceDelete($id)
    {
        // Только для админа
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Только администратор может полностью удалить фото'
            ], 403);
        }

        $photo = Photo::withTrashed()->findOrFail($id);
        $photo->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Фотография полностью удалена из системы'
        ]);
    }

    /**
     * Статистика фотобанка
     */
    public function getStatistics()
    {
        // Только для админа
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_photos' => Photo::count(),
                'approved_photos' => Photo::where('is_approved', true)->count(),
                'pending_photos' => Photo::where('is_approved', false)->count(),
                'deleted_photos' => Photo::onlyTrashed()->count(),
                'total_categories' => PhotoCategory::count(),
                'total_tags' => Tag::count(),
                'total_size_bytes' => Photo::sum('file_size'),
                'total_size_formatted' => $this->formatBytes(Photo::sum('file_size')),
                'total_users_with_photos' => Photo::distinct('user_id')->count('user_id')
            ]
        ]);
    }

    /**
     * Обновление информации о фото
     */
    public function update(Request $request, Photo $photo)
    {
        // Проверка прав
        if (auth()->id() !== $photo->user_id && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для редактирования этого фото'
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:photo_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        DB::beginTransaction();

        try {
            $photo->update([
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id
            ]);

            if ($request->has('tags')) {
                $photo->tags()->sync($request->tags);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Информация обновлена успешно',
                'photo' => $photo->load(['category', 'tags'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Форматирование байтов
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
