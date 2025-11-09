<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {

    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'role_id' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $currentUser = auth()->user();

            // Проверяем права - только владелец компании или менеджер могут создавать пользователей
            $isCompanyOwner = $currentUser->ownedCompanies()->where('id', $request->company_id)->exists();
            $isManager = $currentUser->isManager();

            if (!$isCompanyOwner && !$isManager) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав создавать пользователей'
                ], 403);
            }

            // ВРЕМЕННО: пока нет company_id в users, создаем без него
            // После миграции можно будет добавить 'company_id' => $request->company_id,

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'company_id' => $request->company_id, // теперь можно использовать
                'department_id' => $request->department_id,
                'role_id' => $request->role_id,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            // Добавляем пользователя в отдел если указан
            if ($request->department_id) {
                // Проверяем, что отдел принадлежит компании
                $department = \App\Models\Department::find($request->department_id);
                if ($department->company_id != $request->company_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Выбранный отдел не принадлежит указанной компании'
                    ], 422);
                }

                $user->departments()->attach($request->department_id);
            }

            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'Пользователь успешно создан'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании пользователя: ' . $e->getMessage()
            ], 500);
        }
    }
}
