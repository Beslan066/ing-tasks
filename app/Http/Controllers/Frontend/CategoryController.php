<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'company_id' => 'required|string|max:7',
        ]);

        try {
            $user = auth()->user();

            // Проверяем, что у пользователя есть компания
            if (!$user->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Для создания категории необходимо быть привязанным к компании'
                ], 422);
            }

            $category = Category::create([
                'name' => $request->name,
                'color' => $request->color,
                'company_id' => $user->company_id,
            ]);

            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Категория успешно создана'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании категории: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $category = Category::where('company_id', auth()->user()->company_id)
                ->findOrFail($id);

            return response()->json($category);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Категория не найдена'
            ], 404);
        }
    }

// Метод для обновления категории
    public function update(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        try {
            $user = auth()->user();

            // Находим категорию, принадлежащую компании пользователя
            $category = Category::where('company_id', $user->company_id)
                ->findOrFail($request->category_id);

            $category->update([
                'name' => $request->name,
                'color' => $request->color,
            ]);

            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Категория успешно обновлена'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении категории: ' . $e->getMessage()
            ], 500);
        }
    }
}
