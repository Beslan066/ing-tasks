<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Category;
use App\Models\PhotoCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class PhotobankController extends Controller
{
    public function index(Request $request)
    {
        // Для первоначальной загрузки страницы
        if (!$request->ajax()) {
            $categories = PhotoCategory::all();
            $tags = Tag::all();
            return view('frontend.photobank.index', compact('categories', 'tags'));
        }

        // Асинхронная загрузка фотографий
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
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Фильтрация по тегам
        if ($request->has('tags') && is_array($request->tags)) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->whereIn('tags.id', $request->tags);
            });
        }

        $photos = $query->latest()->paginate(20);

        // Правильно формируем JSON ответ
        $photosData = $photos->map(function($photo) {
            return [
                'id' => $photo->id,
                'title' => $photo->title,
                'description' => $photo->description,
                'file_path' => $photo->file_path,
                'category' => [
                    'id' => $photo->category->id,
                    'name' => $photo->category->name
                ],
                'tags' => $photo->tags->map(function($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name
                    ];
                }),
                'created_at' => $photo->created_at->toDateTimeString()
            ];
        });

        return response()->json([
            'success' => true,
            'photos' => $photosData,
            'next_page_url' => $photos->nextPageUrl(),
            'total' => $photos->total()
        ]);
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
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
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        try {
            $image = $request->file('photo');

            // Простое сохранение без оптимизации
            $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $filePath = 'photos/' . $fileName;

            // Сохраняем оригинальное изображение
            Storage::disk('public')->put($filePath, File::get($image));

            // Создаем запись в базе данных
            $photo = Photo::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_size' => Storage::disk('public')->size($filePath),
                'mime_type' => $image->getMimeType(),
                'category_id' => $request->category_id,
                'user_id' => auth()->id(),
                'metadata' => [
                    'original_name' => $image->getClientOriginalName(),
                    'original_extension' => $image->getClientOriginalExtension(),
                ]
            ]);

            // Привязываем теги
            if ($request->has('tags')) {
                $photo->tags()->sync($request->tags);
            }

            return response()->json([
                'success' => true,
                'message' => 'Фотография успешно загружена и отправлена на модерацию.',
                'photo' => $photo->load(['category', 'tags'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке фотографии: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCategories()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function getTags()
    {
        $tags = Tag::all();
        return response()->json($tags);
    }

}
