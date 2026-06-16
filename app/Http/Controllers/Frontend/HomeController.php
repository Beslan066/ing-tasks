<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Department;
use App\Models\File;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Загружаем необходимые связи
        $user->load(['company', 'ownedCompanies', 'departments']);

        // ПРОВЕРКА 1: Есть ли у пользователя компания?
        $hasCompany = $user->company_id && $user->company;

        // ПРОВЕРКА 2: Является ли пользователь владельцем компании?
        $isOwnerOfCompany = $user->ownedCompanies->isNotEmpty();

        // Если у пользователя нет компании И он не владелец компании - показываем страницу no-companies
        if (!$hasCompany && !$isOwnerOfCompany) {
            return $this->noCompanies();
        }

        // Если у пользователя есть компания, но нет company_id (он владелец)
        if (!$user->company_id && $isOwnerOfCompany) {
            $firstCompany = $user->ownedCompanies->first();
            $user->company_id = $firstCompany->id;
            $user->company = $firstCompany;
        }

        // Проверяем, хочет ли пользователь перейти в режим руководителя
        $adminMode = $request->get('admin_mode', false);

        if ($user->isLeader() && $adminMode === '1') {
            return redirect()->route('tasks.admin');
        }

        // Обновляем статусы просроченных задач
        $this->updateOverdueTasks($user->id);

        // Получаем задачи пользователя по статусам
        $tasksByStatus = [
            // 🔥 ИСПРАВЛЕНО: ВКЛЮЧАЕМ ПРОСРОЧЕННЫЕ ЗАДАЧИ В КОЛОНКУ "НОВЫЕ"
            'new' => Task::with(['author', 'department', 'category', 'files'])
                ->withCount('files')
                ->where('user_id', $user->id)
                ->where(function($query) {
                    $query->where('status', 'назначена')
                        ->orWhere('status', 'просрочена');
                })
                ->orderByRaw("CASE
                WHEN status = 'просрочена' THEN 1
                ELSE 0
            END")
                ->orderBy('deadline', 'asc')
                ->get(),

            'in_progress' => Task::with(['author', 'department', 'category', 'files'])
                ->withCount('files')
                ->where('user_id', $user->id)
                ->where('status', 'в работе')
                ->orderBy('created_at', 'desc')
                ->get(),

            'review' => Task::with(['author', 'department', 'category', 'files'])
                ->withCount('files')
                ->where('user_id', $user->id)
                ->where('status', 'на проверке')
                ->orderBy('created_at', 'desc')
                ->get(),

            'done' => Task::with(['author', 'department', 'category', 'files'])
                ->withCount('files')
                ->where('user_id', $user->id)
                ->where('status', 'выполнена')
                ->orderBy('completed_at', 'desc')
                ->limit(10)
                ->get(),
        ];

        // Статистика
        $stats = [
            'new' => $tasksByStatus['new']->count(),
            'in_progress' => $tasksByStatus['in_progress']->count(),
            'review' => $tasksByStatus['review']->count(),
            'done' => $tasksByStatus['done']->count(),
        ];

        return view('welcome', compact('user', 'tasksByStatus', 'stats'));
    }

    /**
     * Обновляет статусы просроченных задач для пользователя
     */
    private function updateOverdueTasks($userId)
    {
        $tasksToUpdate = Task::where('user_id', $userId)
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->whereNotIn('status', ['выполнена', 'просрочена'])
            ->get();

        foreach ($tasksToUpdate as $task) {
            $task->updateOverdueStatus(); // Этот метод меняет статус на 'просрочена'
        }
    }

    public function home()
    {
        // Если пользователь авторизован - редирект на /home
        if (auth()->check()) {
            return redirect()->route('welcome');
        }

        // Для неавторизованных - показываем обычную страницу
        $usersCount = User::count();
        $companiesCount = Company::count();
        return view('frontend.index', [
            'companiesCount' => $companiesCount,
            'usersCount' => $usersCount,
        ]);
    }

    public function indexAdmin(Request $request, $task = null)
    {
        $user = Auth::user();

        // Проверяем права доступа к админской панели
        if (!$user->isManager()) {
            abort(403, 'У вас нет прав для доступа к панели руководителя');
        }

        // Оптимизируем запросы
        $user->load(['company', 'role', 'departments']);

        // Обновляем статусы просроченных задач
        Task::where('company_id', $user->company_id)
            ->where('status', '!=', 'выполнена')
            ->where('deadline', '<', now())
            ->update(['status' => 'просрочена']);

        // Получаем режим отображения из сессии или запроса
        $viewMode = $request->get('view_mode', session('task_view_mode', 'list'));

        // Определяем базовый запрос (общий для обоих режимов)
        if ($user->isManagerRole() && !$user->isLeader()) {
            // Менеджер видит только задачи своих отделов и где он автор
            $departmentIds = $user->departments()->pluck('departments.id')->toArray();
            $baseQuery = Task::with(['author', 'user', 'department', 'category', 'subtasks']) // Добавил 'subtasks'
            ->withCount('rejections')
                ->where('is_personal', '!=', true)
                ->where('company_id', $user->company_id)
                ->where(function($query) use ($user, $departmentIds) {
                    $query->whereIn('department_id', $departmentIds)
                        ->orWhere('author_id', $user->id);
                });
        } else {
            // Руководитель видит все задачи компании
            $baseQuery = Task::with(['author', 'user', 'department', 'category', 'subtasks']) // Добавил 'subtasks'
            ->withCount('rejections')
                ->where('is_personal', '!=', true)
                ->where('company_id', $user->company_id);
        }

        // Применяем поиск и фильтры к базовому запросу
        $baseQuery = $this->applyFiltersToQuery($baseQuery, $request);

        // Применяем сортировку
        $baseQuery = $this->applySortingToQuery($baseQuery, $request);

        // Для канбан-режима - получаем все задачи (без пагинации)
        if ($viewMode === 'kanban') {
            // Получаем все задачи для канбан-доски (ограничиваем для производительности)
            $allTasks = $baseQuery->limit(500)->get();

            // Группируем задачи по статусам для канбан-доски
            $tasksByStatusForKanban = [
                'просрочена' => collect(),
                'назначена' => collect(),
                'в работе' => collect(),
                'на проверке' => collect(),
                'выполнена' => collect()
            ];

            foreach ($allTasks as $taskItem) {
                $status = $taskItem->status;
                if (isset($tasksByStatusForKanban[$status])) {
                    $tasksByStatusForKanban[$status]->push($taskItem);
                }
            }

            // Создаем объект, совместимый с шаблоном (для пагинации используем коллекцию)
            $tasks = new \Illuminate\Pagination\LengthAwarePaginator(
                $allTasks,
                $allTasks->count(),
                $allTasks->count(),
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Для списка - используем пагинацию (20 задач на страницу)
            $tasks = $baseQuery->paginate(20);
        }

        // Статистика (общая для обоих режимов)
        $stats = [
            'total' => Task::where('company_id', $user->company_id)
                ->where('is_personal', 0)
                ->count(),

            'assigned' => Task::where('company_id', $user->company_id)
                ->where('is_personal', 0)
                ->where('status', Task::STATUS_ASSIGNED)
                ->count(),

            'not_assigned' => Task::where('company_id', $user->company_id)
                ->where('is_personal', 0)
                ->where('status', Task::STATUS_NOT_ASSIGNED)
                ->count(),

            'in_progress' => Task::where('company_id', $user->company_id)
                ->where('is_personal', 0)
                ->where('status', Task::STATUS_IN_PROGRESS)
                ->count(),

            'review' => Task::where('company_id', $user->company_id)
                ->where('is_personal', 0)
                ->where('status', Task::STATUS_REVIEW)
                ->count(),

            'overdue' => Task::where('company_id', $user->company_id)
                ->where('is_personal', 0)
                ->where('status', Task::STATUS_OVERDUE)
                ->count(),

            'completed' => Task::where('company_id', $user->company_id)
                ->where('is_personal', 0)
                ->where('status', Task::STATUS_COMPLETED)
                ->count(),
        ];

        // Данные для фильтров
        $filterData = [
            'users' => User::where('company_id', $user->company_id)->get(),
            'departments' => Department::where('company_id', $user->company_id)->get(),
            'categories' => Category::whereHas('tasks', function($query) use ($user) {
                $query->where('company_id', $user->company_id);
            })->get(),
            'statuses' => Task::getStatuses(),
            'priorities' => ['низкий', 'средний', 'высокий', 'критический'],
        ];

        // Получаем ID задачи для открытия из параметра маршрута или GET
        $openTaskId = $request->route('task') ?? $request->get('open_task');

        // Для канбан-режима передаем дополнительные данные
        if ($viewMode === 'kanban') {
            $tasksByStatus = $tasksByStatusForKanban;
            return view('frontend.main-admin', compact('tasks', 'stats', 'filterData', 'user', 'openTaskId', 'viewMode', 'tasksByStatus'));
        }

        return view('frontend.main-admin', compact('tasks', 'stats', 'filterData', 'user', 'openTaskId', 'viewMode'));
    }

    /**
     * Применяет поиск и фильтры к запросу
     */
    private function applyFiltersToQuery($query, Request $request)
    {
        // Поиск
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Фильтрация по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Фильтрация по приоритету
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Фильтрация по исполнителю
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Фильтрация по отделу
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        // Фильтрация по категории
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        return $query;
    }

    /**
     * Применяет сортировку к запросу
     */
    private function applySortingToQuery($query, Request $request)
    {
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        $allowedSortFields = ['created_at', 'deadline', 'priority', 'name', 'status'];
        $allowedOrders = ['asc', 'desc'];

        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'created_at';
        }
        if (!in_array($order, $allowedOrders)) {
            $order = 'desc';
        }

        return $query->orderBy($sort, $order);
    }

    /**
     * Get kanban tasks data via AJAX (для динамической загрузки)
     */
    public function getKanbanTasksAjax(Request $request)
    {
        try {
            $user = Auth::user();

            // Проверяем права доступа
            if (!$user->isManager()) {
                return response()->json(['error' => 'Нет доступа'], 403);
            }

            // Определяем видимость задач в зависимости от роли
            if ($user->isManagerRole() && !$user->isLeader()) {
                // Менеджер видит только задачи своих отделов и где он автор
                $departmentIds = $user->departments()->pluck('departments.id')->toArray();
                $query = Task::with(['author', 'user', 'department', 'category'])
                    ->withCount('rejections')
                    ->where('is_personal', '!=', true)
                    ->where('company_id', $user->company_id)
                    ->where(function($q) use ($user, $departmentIds) {
                        $q->whereIn('department_id', $departmentIds)
                            ->orWhere('author_id', $user->id);
                    });
            } else {
                // Руководитель видит все задачи компании
                $query = Task::with(['author', 'user', 'department', 'category'])
                    ->withCount('rejections')
                    ->where('is_personal', '!=', true)
                    ->where('company_id', $user->company_id);
            }

            // Применяем фильтры
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('priority') && $request->priority) {
                $query->where('priority', $request->priority);
            }

            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('department_id') && $request->department_id) {
                $query->where('department_id', $request->department_id);
            }

            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Сортировка
            $sort = $request->get('sort', 'created_at');
            $order = $request->get('order', 'desc');
            $allowedSortFields = ['created_at', 'deadline', 'priority', 'name', 'status'];
            if (in_array($sort, $allowedSortFields)) {
                $query->orderBy($sort, $order);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Ограничиваем количество задач для производительности
            $tasks = $query->limit(500)->get();

            // Группируем задачи по статусам
            $kanbanTasks = [
                'просрочена' => [],
                'назначена' => [],
                'в работе' => [],
                'на проверке' => [],
                'выполнена' => [],
            ];

            foreach ($tasks as $task) {
                $status = $task->status;
                if (isset($kanbanTasks[$status])) {
                    $kanbanTasks[$status][] = $task->toArray();
                }
            }

            return response()->json([
                'success' => true,
                'tasks' => $kanbanTasks
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading kanban tasks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки задач: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set the view mode (list or kanban) for the admin panel
     */
    public function setViewMode(Request $request)
    {
        try {
            $request->validate([
                'view_mode' => 'required|in:list,kanban'
            ]);

            // Сохраняем режим просмотра в сессии
            session(['task_view_mode' => $request->view_mode]);

            return response()->json([
                'success' => true,
                'message' => 'Режим просмотра сохранен',
                'view_mode' => $request->view_mode
            ]);

        } catch (\Exception $e) {
            \Log::error('Error saving view mode: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сохранении режима просмотра: ' . $e->getMessage()
            ], 500);
        }
    }

    public function noCompanies()
    {
        $user = Auth::user();

        // Проверяем есть ли активные приглашения для пользователя
        $activeInvitations = \App\Models\Invitation::where('email', $user->email)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->with(['company', 'inviter'])
            ->get();

        return view('frontend.no-companies', compact('user', 'activeInvitations'));
    }

    /**
     * Страница со всеми задачами пользователя (архив)
     */
    public function allTasks(Request $request)
    {
        $user = Auth::user();

        // Получаем ВСЕ задачи (активные + архивные) без фильтрации
        $allTasks = Task::with(['author', 'department', 'category', 'files', 'comments', 'subtasks'])
            ->withTrashed() // Показываем и архивные тоже
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Статистика по ВСЕМ задачам
        $stats = [
            'total' => Task::withTrashed()->where('user_id', $user->id)->count(),
            'new' => Task::withTrashed()->where('user_id', $user->id)->where('status', 'назначена')->count(),
            'in_progress' => Task::withTrashed()->where('user_id', $user->id)->where('status', 'в работе')->count(),
            'review' => Task::withTrashed()->where('user_id', $user->id)->where('status', 'на проверке')->count(),
            'done' => Task::withTrashed()->where('user_id', $user->id)->where('status', 'выполнена')->count(),
            'archived' => Task::onlyTrashed()->where('user_id', $user->id)->count(),
        ];

        return view('frontend.tasks.all-tasks', compact('user', 'allTasks', 'stats'));
    }

    /**
     * Архивировать задачу (мягкое удаление)
     */
    public function archive(Task $task)
    {
        try {
            // Проверяем права: только автор или руководитель может архивировать
            if ($task->author_id !== Auth::id() && !Auth::user()->isLeader()) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав на архивацию этой задачи'
                ], 403);
            }

            // Архивация (мягкое удаление)
            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Задача отправлена в архив'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при архивации: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Восстановить задачу из архива
     */
    public function restore($taskId)
    {
        try {
            $task = Task::onlyTrashed()->findOrFail($taskId);

            // Проверяем права
            if ($task->author_id !== Auth::id() && !Auth::user()->isLeader()) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав на восстановление этой задачи'
                ], 403);
            }

            $task->restore();

            return response()->json([
                'success' => true,
                'message' => 'Задача восстановлена из архива'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при восстановлении: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Полностью удалить задачу (из архива)
     */
    public function forceDelete($taskId)
    {
        try {
            $task = Task::onlyTrashed()->findOrFail($taskId);

            // Проверяем права
            if ($task->author_id !== Auth::id() && !Auth::user()->isLeader()) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав на удаление этой задачи'
                ], 403);
            }

            // Удаляем связанные файлы (опционально)
            foreach ($task->files as $file) {
                // Удаляем физический файл
                if (file_exists(public_path('storage/' . $file->path))) {
                    unlink(public_path('storage/' . $file->path));
                }
                $file->delete();
            }

            $task->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Задача полностью удалена'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Страница со всеми задачами команды (архив)
     */
    public function allTeamTasks(Request $request)
    {
        $user = Auth::user();

        // Получаем все задачи пользователя с пагинацией
        $allTasks = Task::with(['author', 'department', 'category', 'files'])
            ->where('company_id', $user->company_id)
            ->where('is_personal', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10); // По 20 задач на странице

        // Статистика по статусам
        $stats = [
            'total' => Task::where('user_id', $user->id)->count(),
            'new' => Task::where('user_id', $user->id)->where('status', 'назначена')->count(),
            'in_progress' => Task::where('user_id', $user->id)->where('status', 'в работе')->count(),
            'review' => Task::where('user_id', $user->id)->where('status', 'на проверке')->count(),
            'done' => Task::where('user_id', $user->id)->where('status', 'выполнена')->count(),
            'archived' => Task::onlyTrashed()->where('user_id', $user->id)->count(), // если есть мягкое удаление
        ];

        return view('frontend.tasks.all-team-task', compact('user', 'allTasks', 'stats'));
    }


}
