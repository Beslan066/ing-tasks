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
        $departments = Department::all();

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
}
