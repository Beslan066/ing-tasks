<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::query()
            ->where('company_id', auth()->user()->company_id)
            ->get();

        return view('frontend.department.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ]);
        try {
            $department = Department::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
                'supervisor_id' => Auth::id(), // Назначаем текущего пользователя руководителем
                'status' => 'active',
            ]);

            // Добавляем текущего пользователя в отдел
            $department->users()->attach(Auth::id());

            return response()->json([
                'success' => true,
                'department' => $department,
                'message' => 'Отдел успешно создан'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании отдела: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $department = Department::where('supervisor_id', Auth::id()) // Только отделы, где пользователь руководитель
            ->findOrFail($id);

            return response()->json($department);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Отдел не найден или у вас нет прав для редактирования'
            ], 404);
        }
    }

// Метод для обновления отдела
    public function update(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ]);

        try {
            // Находим отдел, где пользователь является руководителем
            $department = Department::where('supervisor_id', Auth::id())
                ->findOrFail($request->department_id);

            $department->update([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            return response()->json([
                'success' => true,
                'department' => $department,
                'message' => 'Отдел успешно обновлен'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении отдела: ' . $e->getMessage()
            ], 500);
        }
    }
}
