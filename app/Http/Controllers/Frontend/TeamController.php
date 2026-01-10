<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\UserRemovedMail;
use App\Models\Department;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        try {
            $authUser = auth()->user();

            $usersQuery = User::query()
                ->where('company_id', $authUser->company_id) // ← ТОЛЬКО пользователи этой компании
                ->with(['role', 'department']);

            // Поиск
            if ($request->has('search') && $request->search != '') {
                $usersQuery->where(function($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('email', 'like', '%'.$request->search.'%');
                });
            }

            // Фильтрация по отделу
            if ($request->has('department') && $request->department != '') {
                $usersQuery->where('department_id', $request->department);
            }

            // Фильтрация по роли
            if ($request->has('role') && $request->role != '') {
                $usersQuery->where('role_id', $request->role);
            }

            // Фильтрация по статусу
            if ($request->has('status') && $request->status != '') {
                $usersQuery->where('is_active', $request->status === 'active');
            }

            // Сортировка
            $sortField = $request->get('sort', 'id');
            $sortDirection = $request->get('direction', 'desc');

            $allowedSortFields = ['id', 'name', 'email', 'created_at', 'is_active'];
            if (in_array($sortField, $allowedSortFields)) {
                $usersQuery->orderBy($sortField, $sortDirection);
            } else {
                $usersQuery->orderBy('id', 'desc');
            }

            $users = $usersQuery->paginate(20);

            // Получаем отделы и роли для фильтров
            $departments = Department::where('company_id', $authUser->company_id)->get();
            $roles = Role::where('company_id', $authUser->company_id)->get();

            return view('frontend.teams.index', [
                'users' => $users,
                'departments' => $departments,
                'roles' => $roles,
                'currentSort' => $sortField,
                'currentDirection' => $sortDirection,
            ]);

        } catch (\Exception $e) {
            \Log::error('Team index error: ' . $e->getMessage());
            return back()->with('error', 'Произошла ошибка при загрузке страницы');
        }
    }


    public function destroy(Request $request, $id)
    {
        try {
            \Log::info('=== УДАЛЕНИЕ ПОЛЬЗОВАТЕЛЯ ИЗ КОМПАНИИ ===');

            $authUser = auth()->user();
            $userToRemove = User::where('company_id', $authUser->company_id)
                ->findOrFail($id);

            \Log::info('Данные:', [
                'auth_user_id' => $authUser->id,
                'target_user_id' => $userToRemove->id,
                'company_id' => $authUser->company_id
            ]);

            // ПРОВЕРКИ
            if ($userToRemove->id === $authUser->id) {
                \Log::warning('Попытка удалить себя');
                return back()->with('error', 'Вы не можете удалить себя');
            }

            if ($userToRemove->isCompanyOwner()) {
                \Log::warning('Попытка удалить владельца компании');
                return back()->with('error', 'Нельзя удалить владельца компании');
            }

            if (!$this->canRemoveUser($authUser, $userToRemove)) {
                \Log::warning('Нет прав на удаление');
                return back()->with('error', 'У вас нет прав для удаления этого пользователя');
            }

            DB::beginTransaction();

            try {
                // 1. ОТПРАВКА ПИСЬМА
                \Log::info('Отправка email...');
                Mail::to($userToRemove->email)->send(
                    new UserRemovedMail($authUser->company, $userToRemove->name)
                );
                \Log::info('Email отправлен');

                // 2. ПЕРЕНАЗНАЧЕНИЕ АКТИВНЫХ ЗАДАЧ
                $activeTasks = $userToRemove->assignedTasks()
                    ->where('status', '!=', 'выполнена')
                    ->where('company_id', $authUser->company_id)
                    ->get();

                \Log::info('Активных задач для переназначения: ' . $activeTasks->count());

                foreach ($activeTasks as $task) {
                    // Переназначаем на автора задачи или на руководителя
                    if ($task->author_id && $task->author_id !== $userToRemove->id) {
                        $task->update(['user_id' => $task->author_id]);
                        \Log::info("Задача {$task->id} переназначена на автора: {$task->author_id}");
                    } elseif ($task->department && $task->department->supervisor_id) {
                        $task->update(['user_id' => $task->department->supervisor_id]);
                        \Log::info("Задача {$task->id} переназначена на руководителя отдела: {$task->department->supervisor_id}");
                    } else {
                        $task->update(['user_id' => $authUser->id]);
                        \Log::info("Задача {$task->id} переназначена на вас: {$authUser->id}");
                    }
                }

                // 3. ОТКЛЮЧАЕМ ПОЛЬЗОВАТЕЛЯ ОТ ОТДЕЛОВ (многие-ко-многим)
                if ($userToRemove->departments()->exists()) {
                    $count = $userToRemove->departments()->detach();
                    \Log::info("Удалено из отделов (многие-ко-многим): {$count}");
                }

                // 4. УБИРАЕМ ИЗ РУКОВОДСТВА ОТДЕЛАМИ ЭТОЙ КОМПАНИИ
                $supervisedDepartments = $userToRemove->supervisedDepartments()
                    ->where('company_id', $authUser->company_id)
                    ->get();

                if ($supervisedDepartments->count() > 0) {
                    $count = \App\Models\Department::where('supervisor_id', $userToRemove->id)
                        ->where('company_id', $authUser->company_id)
                        ->update(['supervisor_id' => null]);
                    \Log::info("Убрано из руководства отделов: {$count}");
                }

                // 5. СБРАСЫВАЕМ СВЯЗИ С КОМПАНИЕЙ И ОТДЕЛОМ
                $updateData = [
                    'company_id' => null,  // ← ОСНОВНОЕ: убираем из компании
                    'department_id' => null, // убираем из отдела
                    'role_id' => null,      // сбрасываем роль в компании
                ];

                \Log::info('Обновляемые данные:', $updateData);

                // Обновляем пользователя - ТОЛЬКО связи с компанией
                $updated = $userToRemove->update($updateData);

                \Log::info('Результат обновления:', ['updated' => $updated]);

                // 6. ОЧИЩАЕМ ТОКЕНЫ (если используете Sanctum/Passport)
                if (method_exists($userToRemove, 'tokens')) {
                    $tokensCount = $userToRemove->tokens()->delete();
                    \Log::info("Удалено токенов: {$tokensCount}");
                }

                DB::commit();

                // Перезагружаем модель, чтобы убедиться в изменениях
                $userToRemove->refresh();

                \Log::info('=== УДАЛЕНИЕ УСПЕШНО ===');
                \Log::info('После обновления:', [
                    'company_id' => $userToRemove->company_id,
                    'department_id' => $userToRemove->department_id,
                    'is_active' => $userToRemove->is_active // остается true
                ]);

                return back()->with('success', 'Пользователь успешно удален из команды. Уведомление отправлено.');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Ошибка в транзакции: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
                return back()->with('error', 'Произошла ошибка: ' . $e->getMessage());
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Пользователь не найден: ' . $e->getMessage());
            return back()->with('error', 'Пользователь не найден');
        } catch (\Exception $e) {
            \Log::error('Общая ошибка: ' . $e->getMessage());
            return back()->with('error', 'Произошла ошибка: ' . $e->getMessage());
        }
    }

    /**
     * Проверка прав на удаление пользователя
     */
    private function canRemoveUser($authUser, $userToRemove): bool
    {
        // Владелец компании может удалить кого угодно (кроме другого владельца)
        if ($authUser->isCompanyOwner()) {
            return true;
        }

        // Руководитель/менеджер может удалить только обычных сотрудников
        if ($authUser->isManager() || $authUser->isManagerRole()) {
            // Не может удалить другого руководителя/менеджера или владельца
            if ($userToRemove->isCompanyOwner() ||
                $userToRemove->isManager() ||
                $userToRemove->isManagerRole()) {
                return false;
            }

            // Может удалить только из своих отделов
            $manageableDepartmentIds = $authUser->getManageableDepartments()
                ->pluck('id')
                ->toArray();

            return in_array($userToRemove->department_id, $manageableDepartmentIds);
        }

        return false;
    }

    /**
     * Переназначение активных задач пользователя
     */
    private function reassignUserTasks($user, $authUser)
    {
        $activeTasks = $user->assignedTasks()
            ->where('status', '!=', 'выполнена')
            ->get();

        foreach ($activeTasks as $task) {
            // Вариант 1: Назначить на руководителя, который удаляет
            // $task->update(['user_id' => $authUser->id]);

            // Вариант 2: Назначить на автора задачи
            if ($task->author_id) {
                $task->update(['user_id' => $task->author_id]);
            }
            // Вариант 3: Назначить на руководителя отдела
            else if ($task->department && $task->department->supervisor_id) {
                $task->update(['user_id' => $task->department->supervisor_id]);
            }
            // Вариант 4: Назначить на того, кто удаляет
            else {
                $task->update(['user_id' => $authUser->id]);
            }

            Log::info('Задача переназначена', [
                'task_id' => $task->id,
                'old_user' => $user->id,
                'new_user' => $task->user_id,
            ]);
        }

        return $activeTasks->count();
    }

    /**
     * Массовое удаление пользователей
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $authUser = auth()->user();
            $removedCount = 0;

            foreach ($request->user_ids as $userId) {
                $userToRemove = User::where('company_id', $authUser->company_id)
                    ->find($userId);

                if (!$userToRemove) {
                    continue;
                }

                // Проверка прав
                if (!$this->canRemoveUser($authUser, $userToRemove)) {
                    continue;
                }

                // Нельзя удалить себя
                if ($userToRemove->id === $authUser->id) {
                    continue;
                }

                DB::transaction(function () use ($userToRemove, $authUser) {
                    // Отправка письма
                    Mail::to($userToRemove->email)->send(
                        new UserRemovedMail($authUser->company, $userToRemove->name)
                    );

                    // Переназначение задач
                    $this->reassignUserTasks($userToRemove, $authUser);

                    // Деактивация
                    $userToRemove->update([
                        'is_active' => false,
                        'company_id' => null,
                        'department_id' => null,
                        'role_id' => null,
                    ]);
                });

                $removedCount++;
            }

            return back()->with('success', "Успешно удалено $removedCount пользователей");

        } catch (\Exception $e) {
            Log::error('Ошибка при массовом удалении: ' . $e->getMessage());
            return back()->with('error', 'Произошла ошибка при удалении пользователей');
        }
    }

    public function exportTable(Request $request)
    {
        try {
            $authUser = auth()->user();
            $format = $request->get('format', 'excel');

            $usersQuery = User::query()
                ->where('company_id', $authUser->company_id)
                ->with(['role', 'department']);

            // Применяем те же фильтры, что и в index
            if ($request->has('search') && $request->search != '') {
                $usersQuery->where(function($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('email', 'like', '%'.$request->search.'%');
                });
            }

            if ($request->has('department') && $request->department != '') {
                $usersQuery->where('department_id', $request->department);
            }

            if ($request->has('role') && $request->role != '') {
                $usersQuery->where('role_id', $request->role);
            }

            if ($request->has('status') && $request->status != '') {
                $usersQuery->where('is_active', $request->status === 'active');
            }

            $users = $usersQuery->get();

            if ($format === 'excel') {
                return $this->exportTableToExcel($users);
            } elseif ($format === 'pdf') {
                return $this->exportTableToPdf($users);
            }

            return back()->with('error', 'Неверный формат экспорта');

        } catch (\Exception $e) {
            \Log::error('Export table error: ' . $e->getMessage());
            return back()->with('error', 'Ошибка при экспорте таблицы');
        }
    }

    private function exportTableToExcel($users)
    {
        $fileName = "команда_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');

            // Добавляем BOM для корректного отображения кириллицы в Excel
            fwrite($file, "\xEF\xBB\xBF");

            // Заголовки
            fputcsv($file, [
                'ID', 'Имя', 'Email', 'Роль', 'Отдел',
                'Всего задач', 'Выполнено', '% выполнения', 'Просрочено',
                'Статус', 'Дата регистрации'
            ], ';');

            // Данные
            foreach ($users as $user) {
                $stats = $user->getTaskCompletionStats();
                $overdue = $user->assignedTasks()
                    ->where('status', '!=', 'выполнена')
                    ->where('deadline', '<', now())
                    ->count();

                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role ? $user->role->name : '',
                    $user->department ? $user->department->name : '',
                    $stats['total'],
                    $stats['completed'],
                    $stats['completion_rate'] . '%',
                    $overdue,
                    $user->is_active ? 'Активный' : 'Неактивный',
                    $user->created_at->format('d.m.Y')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportTableToPdf($users)
    {
        // Для PDF можно использовать dompdf, но пока вернем CSV
        // В реальном проекте нужно установить и настроить dompdf
        return $this->exportTableToExcel($users);
    }

    public function printTable(Request $request)
    {
        try {
            $authUser = auth()->user();

            $usersQuery = User::query()
                ->where('company_id', $authUser->company_id)
                ->with(['role', 'department']);

            // Применяем фильтры
            if ($request->has('search') && $request->search != '') {
                $usersQuery->where(function($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('email', 'like', '%'.$request->search.'%');
                });
            }

            if ($request->has('department') && $request->department != '') {
                $usersQuery->where('department_id', $request->department);
            }

            if ($request->has('status') && $request->status != '') {
                $usersQuery->where('is_active', $request->status === 'active');
            }

            $users = $usersQuery->orderBy('created_at', 'desc')->get();

            return view('frontend.teams.print', [
                'users' => $users,
                'printDate' => now()->format('d.m.Y H:i'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Print table error: ' . $e->getMessage());
            return back()->with('error', 'Ошибка при подготовке печати');
        }
    }

    public function getUserDetails($userId)
    {
        try {
            $authUser = auth()->user();

            $user = User::with(['role', 'department', 'company'])
                ->where('company_id', $authUser->company_id)
                ->withCount([
                    'assignedTasks as total_tasks_count',
                    'assignedTasks as completed_tasks_count' => function($query) {
                        $query->where('status', 'выполнена');
                    },
                    'assignedTasks as overdue_tasks_count' => function($query) {
                        $query->where('status', '!=', 'выполнена')
                            ->where('deadline', '<', now());
                    },
                    'assignedTasks as in_progress_tasks_count' => function($query) {
                        $query->where('status', 'в работе');
                    }
                ])
                ->findOrFail($userId);

            if ($user->company_id !== $authUser->company_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Доступ запрещен'
                ], 403);
            }

            $averageCompletionRate = $user->getAverageCompletionRate();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_url, // Вот это важно!
                    'avatar' => $user->avatar,
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at,
                    'last_login_at' => $user->last_login_at,
                    'role' => $user->role ? [
                        'id' => $user->role->id,
                        'name' => $user->role->name,
                    ] : null,
                    'department' => $user->department ? [
                        'id' => $user->department->id,
                        'name' => $user->department->name,
                    ] : null,
                    'company' => $user->company ? [
                        'id' => $user->company->id,
                        'name' => $user->company->name,
                    ] : null,
                ],
                'completion_rate' => $averageCompletionRate,
                'stats' => [
                    'total_tasks' => $user->total_tasks_count,
                    'completed_tasks' => $user->completed_tasks_count,
                    'overdue_tasks' => $user->overdue_tasks_count,
                    'in_progress_tasks' => $user->in_progress_tasks_count,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Get user details error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Ошибка при загрузке данных пользователя'
            ], 500);
        }
    }

    public function printUserDetails($userId)
    {
        try {
            $authUser = auth()->user();

            $user = User::with(['role', 'department', 'company'])
                ->where('company_id', $authUser->company_id)
                ->withCount([
                    'assignedTasks as total_tasks_count',
                    'assignedTasks as completed_tasks_count',
                    'assignedTasks as overdue_tasks_count',
                    'assignedTasks as in_progress_tasks_count'
                ])
                ->findOrFail($userId);

            if ($user->company_id !== $authUser->company_id) {
                abort(403, 'Доступ запрещен');
            }

            $averageCompletionRate = $user->getAverageCompletionRate();

            return view('frontend.teams.print-user', [
                'user' => $user,
                'completion_rate' => $averageCompletionRate,
                'printDate' => now()->format('d.m.Y H:i'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Print user details error: ' . $e->getMessage());
            abort(500, 'Ошибка при подготовке печати');
        }
    }

    public function getUserTasks($userId, Request $request)
    {
        try {
            $authUser = auth()->user();

            $user = User::where('company_id', $authUser->company_id)
                ->findOrFail($userId);

            $period = $request->get('period', 'month');

            $query = Task::where('user_id', $userId)
                ->with(['category', 'department', 'author'])
                ->where('company_id', $authUser->company_id);

            switch ($period) {
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->startOfMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', Carbon::now()->startOfYear());
                    break;
                case 'custom':
                    if ($request->has('start_date') && $request->has('end_date')) {
                        $query->whereBetween('created_at', [
                            Carbon::parse($request->start_date)->startOfDay(),
                            Carbon::parse($request->end_date)->endOfDay()
                        ]);
                    }
                    break;
            }

            $tasks = $query->orderBy('created_at', 'desc')->get();
            $periodCompletionRate = $user->getAverageCompletionRate($period);

            return response()->json([
                'success' => true,
                'tasks' => $tasks,
                'period' => $period,
                'period_completion_rate' => $periodCompletionRate
            ]);

        } catch (\Exception $e) {
            \Log::error('Get user tasks error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Ошибка при загрузке задач'
            ], 500);
        }
    }

    public function exportUserStats($userId, Request $request)
    {
        try {
            $authUser = auth()->user();

            $user = User::where('company_id', $authUser->company_id)
                ->findOrFail($userId);

            $period = $request->get('period', 'month');
            $type = $request->get('type', 'excel');

            $stats = $this->calculateUserStats($user, $period);

            if ($type === 'excel') {
                return $this->exportToExcel($user, $stats, $period);
            } elseif ($type === 'pdf') {
                return $this->exportToPdf($user, $stats, $period);
            }

            return response()->json([
                'success' => false,
                'error' => 'Неверный тип экспорта'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Export user stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Ошибка при экспорте данных'
            ], 500);
        }
    }

    private function calculateUserStats($user, $period)
    {
        $query = $user->assignedTasks();

        switch ($period) {
            case 'week':
                $query->where('created_at', '>=', Carbon::now()->startOfWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', Carbon::now()->startOfMonth());
                break;
            case 'year':
                $query->where('created_at', '>=', Carbon::now()->startOfYear());
                break;
        }

        $totalTasks = $query->count();
        $completedTasks = $query->where('status', 'выполнена')->count();
        $completionRate = $user->getAverageCompletionRate($period);

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'completion_rate' => $completionRate,
            'overdue_tasks' => $user->assignedTasks()
                ->where('status', '!=', 'выполнена')
                ->where('deadline', '<', now())
                ->when($period !== 'all', function($q) use ($period) {
                    switch ($period) {
                        case 'week':
                            $q->where('created_at', '>=', Carbon::now()->startOfWeek());
                            break;
                        case 'month':
                            $q->where('created_at', '>=', Carbon::now()->startOfMonth());
                            break;
                        case 'year':
                            $q->where('created_at', '>=', Carbon::now()->startOfYear());
                            break;
                    }
                })
                ->count(),
        ];
    }

    private function exportToExcel($user, $stats, $period)
    {
        $fileName = "статистика_{$user->name}_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($user, $stats, $period) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, ['Статистика пользователя: ' . $user->name], ';');
            fputcsv($file, ['Период: ' . $this->getPeriodName($period)], ';');
            fputcsv($file, ['Дата экспорта: ' . now()->format('d.m.Y H:i')], ';');
            fputcsv($file, [], ';');

            fputcsv($file, ['Показатель', 'Значение'], ';');
            fputcsv($file, ['Всего задач', $stats['total_tasks']], ';');
            fputcsv($file, ['Выполнено задач', $stats['completed_tasks']], ';');
            fputcsv($file, ['Просрочено задач', $stats['overdue_tasks']], ';');
            fputcsv($file, ['Средний % выполнения', $stats['completion_rate'] . '%'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToPdf($user, $stats, $period)
    {
        // Временная реализация - возвращаем CSV
        return $this->exportToExcel($user, $stats, $period);
    }

    private function getPeriodName($period)
    {
        return match($period) {
            'week' => 'Неделя',
            'month' => 'Месяц',
            'year' => 'Год',
            'all' => 'Все время',
            default => 'Произвольный период'
        };
    }
}
