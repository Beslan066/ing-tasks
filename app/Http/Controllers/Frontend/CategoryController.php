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
        ]);

        try {
            $category = Category::create([
                'name' => $request->name,
                'color' => $request->color,
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
}
