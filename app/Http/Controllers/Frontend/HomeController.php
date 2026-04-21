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

        // ДЕТАЛЬНАЯ ОТЛАДКА - временно, чтобы увидеть проблему
        // \Log::info('User data:', [
        //     'user_id' => $user->id,
        //     'company_id' => $user->company_id,
        //     'has_company' => (bool)$user->company,
        //     'owned_companies_count' => $user->ownedCompanies->count()
        // ]);

        // ПРОВЕРКА 1: Есть ли у пользователя компания?
        $hasCompany = $user->company_id && $user->company;

        // ПРОВЕРКА 2: Является ли пользователь владельцем компании?
        $isOwnerOfCompany = $user->ownedCompanies->isNotEmpty();

        // Если у пользователя нет компании И он не владелец компании - показываем страницу no-companies
        if (!$hasCompany && !$isOwnerOfCompany) {
            return $this->noCompanies();
        }

        // Если у пользователя есть компания, но нет company_id (он владелец)
        // Нужно установить company_id для дальнейшей работы
        if (!$user->company_id && $isOwnerOfCompany) {
            // Берем первую компанию, которой владеет пользователь
            $firstCompany = $user->ownedCompanies->first();
            $user->company_id = $firstCompany->id;
            $user->company = $firstCompany;
        }

        // Проверяем, хочет ли пользователь перейти в режим руководителя
        $adminMode = $request->get('admin_mode', false);

        // Если пользователь руководитель И запросил режим руководителя - перенаправляем на админку
        if ($user->isLeader() && $adminMode === '1') {
            return redirect()->route('tasks.admin');
        }

        // Получаем задачи пользователя по статусам
        $tasksByStatus = [
            'new' => Task::with(['author', 'department', 'category', 'files'])
                ->where('user_id', $user->id)
                ->where('status', 'назначена')
                ->orderBy('created_at', 'desc')
                ->get(),

            'in_progress' => Task::with(['author', 'department', 'category', 'files'])
                ->where('user_id', $user->id)
                ->where('status', 'в работе')
                ->orderBy('created_at', 'desc')
                ->get(),

            'review' => Task::with(['author', 'department', 'category', 'files'])
                ->where('user_id', $user->id)
                ->where('status', 'на проверке')
                ->orderBy('created_at', 'desc')
                ->get(),

            'done' => Task::with(['author', 'department', 'category', 'files'])
                ->where('user_id', $user->id)
                ->where('status', 'выполнена')
                ->orderBy('created_at', 'desc')
                ->get(),
        ];

        // Задачи отделов (доступные для взятия) - ИСПРАВЛЕНО
        $availableTasks = collect();
        if ($user->departments()->count() > 0) {
            $departmentIds = $user->departments()->pluck('departments.id')->toArray();
            $availableTasks = Task::with(['author', 'departments', 'category'])
                ->whereIn('department_id', $departmentIds)
                ->whereNull('user_id')
                ->where('status', 'не назначена')
                ->where('is_personal', 0)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Личные задачи пользователя
        $personalTasks = Task::with(['author', 'department', 'category', 'files'])
            ->where('author_id', $user->id)
            ->where('user_id', $user->id)
            ->where('is_personal', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Статистика
        $stats = [
            'new' => $tasksByStatus['new']->count(),
            'in_progress' => $tasksByStatus['in_progress']->count(),
            'review' => $tasksByStatus['review']->count(),
            'done' => $tasksByStatus['done']->count(),
            'available' => $availableTasks->count(),
            'personal' => $personalTasks->count(),
        ];

        return view('welcome', compact('user', 'tasksByStatus', 'availableTasks', 'personalTasks', 'stats'));
    }

    public function home()
    {

        $usersCount = User::count();
        $companiesCount = Company::count();
        return view('frontend.index', [
            'companiesCount' => $companiesCount,
            'usersCount' => $usersCount,
        ]);
    }

    public function indexAdmin(Request $request)
    {
        $user = Auth::user();

        // Проверяем права доступа к админской панели
        if (!$user->isManager()) {
            abort(403, 'У вас нет прав для доступа к панели руководителя');
        }

        // Оптимизируем запросы
        $user->load(['company', 'role', 'departments']); // ИСПРАВЛЕНО: добавил departments

        // Определяем видимость задач в зависимости от роли
        if ($user->isManagerRole() && !$user->isLeader()) {
            // Менеджер видит только задачи своих отделов и где он автор - ИСПРАВЛЕНО
            $departmentIds = $user->departments()->pluck('departments.id')->toArray();
            $tasksQuery = Task::with(['author', 'user', 'department', 'category'])
                ->withCount('rejections')
                ->where('company_id', $user->company_id)
                ->where(function($query) use ($user, $departmentIds) {
                    $query->whereIn('department_id', $departmentIds)
                        ->orWhere('author_id', $user->id);
                });
        } else {
            // Руководитель видит все задачи компании
            $tasksQuery = Task::with(['author', 'user', 'department', 'category'])
                ->withCount('rejections')
                ->where('company_id', $user->company_id);
        }

        // Базовый запрос - ВСЕ задачи компании пользователя (НЕ дублируем, убираем лишний)
        // Удаляем этот дублирующий блок, так как выше уже есть $tasksQuery

        // Поиск
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $tasksQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Фильтрация по статусу
        if ($request->has('status') && $request->status) {
            $tasksQuery->where('status', $request->status);
        }

        // Фильтрация по приоритету
        if ($request->has('priority') && $request->priority) {
            $tasksQuery->where('priority', $request->priority);
        }

        // Фильтрация по исполнителю
        if ($request->has('user_id') && $request->user_id) {
            $tasksQuery->where('user_id', $request->user_id);
        }

        // Фильтрация по отделу
        if ($request->has('department_id') && $request->department_id) {
            $tasksQuery->where('department_id', $request->department_id);
        }

        // Фильтрация по категории
        if ($request->has('category_id') && $request->category_id) {
            $tasksQuery->where('category_id', $request->category_id);
        }

        // Сортировка
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

        $tasksQuery->orderBy($sort, $order);

        $tasks = $tasksQuery->paginate(10);

        // Статистика
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

        return view('frontend.main-admin', compact('tasks', 'stats', 'filterData', 'user'));
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
}
