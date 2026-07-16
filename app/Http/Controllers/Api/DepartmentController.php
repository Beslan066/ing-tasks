<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }

            // Проверяем, есть ли у пользователя компания
            if (!$user->company_id) {
                return response()->json([
                    'success' => true,
                    'departments' => []
                ]);
            }

            $departments = Department::where('company_id', $user->company_id)
                ->where('status', 'active')
                ->with('supervisor')
                ->get()
                ->map(function ($department) {
                    return [
                        'id' => $department->id,
                        'name' => $department->name,
                        'supervisor' => $department->supervisor?->name,
                        'users_count' => $department->getUsersCount(),
                    ];
                });

            return response()->json([
                'success' => true,
                'departments' => $departments,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading departments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки отделов: ' . $e->getMessage()
            ], 500);
        }
    }
}
