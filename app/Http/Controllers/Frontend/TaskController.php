<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\File;
use App\Models\TaskRejection;
use App\Models\User;
use App\Models\Department;
use App\Models\Category;
use App\Models\Company;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->canViewAllCompanyTasks()) {
            return redirect()->route('tasks.admin');
        }

        $tasks = $user->assignedTasks()
            ->with(['author', 'department', 'category', 'files'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.tasks.index', compact('tasks', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->canViewAllCompanyTasks()) {
            abort(403, 'У вас нет прав для создания задач');
        }

        $departments = Department::where('company_id', $user->company_id)->get();
        $categories = Category::where('company_id', $user->company_id)->get();

        // Пользователи, которым можно назначить задачу
        $assignableUsers = User::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->get();

        return view('frontend.tasks.create', compact('departments', 'categories', 'assignableUsers', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('=== START TASK STORE ===');
        \Log::info('Request data:', $request->all());
        \Log::info('Files:', $request->hasFile('files') ? ['has_files' => true] : ['has_files' => false]);

        $user = Auth::user();
        \Log::info('User:', [
            'id' => $user->id,
            'name' => $user->name,
            'company_id' => $user->company_id,
            'canViewAllCompanyTasks' => $user->canViewAllCompanyTasks()
        ]);

        if (!$user->canViewAllCompanyTasks()) {
            \Log::warning('User does not have permission to create tasks');
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для создания задач'
            ], 403);
        }

        // Валидация
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'category_id' => 'nullable|exists:categories,id',
            'user_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:низкий,средний,высокий,критический',
            'status' => 'required|in:назначена,не назначена,в работе,просрочена,на проверке,выполнена',
            'deadline' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'files.*' => 'nullable|file|max:10240',
            'subtasks.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Ошибки валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Проверяем отдел
            $department = Department::find($request->department_id);
            \Log::info('Department check:', [
                'department_id' => $request->department_id,
                'found' => !!$department,
                'department_company_id' => $department ? $department->company_id : null,
                'user_company_id' => $user->company_id
            ]);

            if (!$department || $department->company_id !== $user->company_id) {
                \Log::warning('Department does not belong to user company');
                return response()->json([
                    'success' => false,
                    'message' => 'Выбранный отдел не принадлежит вашей компании'
                ], 422);
            }

            // Проверяем исполнителя
            $assignedUser = null;
            if ($request->user_id) {
                $assignedUser = User::find($request->user_id);
                \Log::info('Assigned user check:', [
                    'user_id' => $request->user_id,
                    'found' => !!$assignedUser,
                    'user_company_id' => $assignedUser ? $assignedUser->company_id : null
                ]);

                if (!$assignedUser || $assignedUser->company_id !== $user->company_id) {
                    \Log::warning('Assigned user does not belong to user company');
                    return response()->json([
                        'success' => false,
                        'message' => 'Выбранный исполнитель не работает в вашей компании'
                    ], 422);
                }
            }

            // Создаем задачу
            $taskData = [
                'name' => $request->name,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'category_id' => $request->category_id,
                'user_id' => $request->user_id,
                'priority' => $request->priority,
                'status' => $request->status,
                'deadline' => $request->deadline,
                'estimated_hours' => $request->estimated_hours,
                'company_id' => $user->company_id,
                'author_id' => $user->id,
            ];

            \Log::info('Creating task with data:', $taskData);
            $task = Task::create($taskData);
            \Log::info('Task created:', ['task_id' => $task->id]);

            // Отправляем уведомление
            if ($assignedUser && $task->user_id) {
                try {
                    \Log::info('Sending notification to user:', ['user_id' => $assignedUser->id, 'email' => $assignedUser->email]);
                    $assignedUser->notify(new TaskAssignedNotification($task));
                    \Log::info('Notification sent successfully');
                } catch (\Exception $e) {
                    \Log::error('Notification error: ' . $e->getMessage());
                }
            }

            // Обрабатываем файлы
            if ($request->hasFile('files')) {
                \Log::info('Processing files');
                foreach ($request->file('files') as $file) {
                    $path = $file->store('tasks/' . $task->id, 'public');
                    \Log::info('File stored:', ['path' => $path]);

                    File::create([
                        'name' => $file->getClientOriginalName(),
                        'file' => $path,
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                        'department_id' => $task->department_id,
                    ]);
                }
            }

            // Обрабатываем подзадачи
            if ($request->has('subtasks')) {
                \Log::info('Processing subtasks:', ['count' => count($request->subtasks)]);
                foreach ($request->subtasks as $subtaskName) {
                    if (!empty(trim($subtaskName))) {
                        Task::create([
                            'name' => trim($subtaskName),
                            'parent_id' => $task->id,
                            'company_id' => $user->company_id,
                            'department_id' => $task->department_id,
                            'author_id' => $user->id,
                            'status' => 'не назначена',
                            'priority' => $task->priority,
                        ]);
                    }
                }
            }

            \Log::info('=== TASK CREATED SUCCESSFULLY ===');
            return response()->json([
                'success' => true,
                'message' => 'Задача успешно создана',
                'task' => $task->load(['department', 'category', 'user', 'author', 'files'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in task store: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании задачи: ' . $e->getMessage()
            ], 500);
        }
    }


    public function view(Task $task)
    {
        $user = Auth::user();

        try {
            // Проверяем доступ к задаче
            if (!$this->canAccessTask($user, $task)) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этой задаче'
                ], 403);
            }

            // Загружаем все необходимые отношения
            $task->load(['author', 'user', 'department', 'category', 'files']);

            // Добавляем иконку статуса для отображения
            $task->status_icon = $task->getStatusIcon();

            return response()->json([
                'success' => true,
                'task' => $task
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при загрузке задачи: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке задачи'
            ], 500);
        }
    }

    /**
     * Получить данные задачи для редактирования (админ)
     */
    public function getTask(Task $task)
    {
        $user = Auth::user();

        // Проверяем права доступа
        if (!$user->canViewAllCompanyTasks()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для редактирования задач'
            ], 403);
        }

        // Проверяем, что задача принадлежит компании пользователя
        if ($task->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Задача не принадлежит вашей компании'
            ], 403);
        }

        // Загружаем отказы с пользователями
        $task->load(['author', 'user', 'department', 'category', 'files', 'rejections.user']);

        return response()->json([
            'success' => true,
            'task' => $task,
            'departments' => Department::where('company_id', $user->company_id)->get(),
            'categories' => Category::where('company_id', $user->company_id)->get(),
            'users' => User::where('company_id', $user->company_id)->get()
        ]);
    }
    /**
     * Take available task (for employees)
     */
    public function takeTask(Task $task)
    {
        $user = Auth::user();

        try {
            // Проверяем, что задача доступна для взятия
            if ($task->user_id !== null || $task->status !== 'не назначена') {
                return response()->json([
                    'success' => false,
                    'message' => 'Задача уже назначена или недоступна'
                ], 422);
            }

            // Проверяем, что пользователь из того же отдела
            if ($task->department_id !== $user->department_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы не можете взять задачу из другого отдела'
                ], 422);
            }

            // Назначаем задачу пользователю
            $task->update([
                'user_id' => $user->id,
                'status' => 'в работе'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Задача успешно взята в работу',
                'task' => $task->load(['department', 'category', 'author'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при взятии задачи: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при взятии задачи'
            ], 500);
        }
    }

    /**
     * Update task status (for employees)
     */
    public function updateTaskStatus(Request $request, Task $task)
    {
        $user = Auth::user();

        try {
            // Проверяем, что пользователь является исполнителем задачи
            if ($task->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы не являетесь исполнителем этой задачи'
                ], 403);
            }

            $request->validate([
                'status' => 'required|in:в работе,на проверке,выполнена',
                'actual_hours' => 'nullable|numeric|min:0'
            ]);

            $updateData = ['status' => $request->status];

            // Если отправляется на проверку, обновляем фактическое время
            if ($request->status === 'на проверке' && $request->has('actual_hours')) {
                $updateData['actual_hours'] = $request->actual_hours;
            }

            // Если задача выполняется, обновляем время выполнения
            if ($request->status === 'выполнена') {
                $updateData['completed_at'] = now();
            }

            $task->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Статус задачи успешно обновлен',
                'task' => $task->load(['department', 'category', 'author'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при обновлении статуса задачи: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении статуса задачи'
            ], 500);
        }
    }



    /**
     * Reject task (for employees)
     */
    public function rejectTask(Request $request, Task $task)
    {
        $user = Auth::user();

        try {
            // Проверяем, что пользователь является исполнителем задачи
            if ($task->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы не являетесь исполнителем этой задачи'
                ], 403);
            }

            $request->validate([
                'reason' => 'required|string|max:1000'
            ]);

            // Создаем запись об отказе
            TaskRejection::create([
                'reason' => $request->reason,
                'task_id' => $task->id,
                'user_id' => $user->id,
                'company_id' => $user->company_id
            ]);

            // Освобождаем задачу и меняем статус
            $task->update([
                'user_id' => null,
                'status' => 'не назначена'
            ]);

            // Логируем отказ
            \Log::info("Пользователь {$user->name} отказался от задачи #{$task->id}. Причина: {$request->reason}");

            return response()->json([
                'success' => true,
                'message' => 'Вы отказались от задачи',
                'task' => $task->load(['department', 'category', 'author'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при отказе от задачи: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отказе от задачи'
            ], 500);
        }
    }

    /**
     * Attach file to task (for employees)
     */
    public function attachFile(Request $request, Task $task)
    {
        $user = Auth::user();

        try {
            // Проверяем, что пользователь имеет доступ к задаче
            if ($task->user_id !== $user->id && !$user->canViewAllCompanyTasks()) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этой задаче'
                ], 403);
            }

            $request->validate([
                'file' => 'required|file|max:10240',
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('tasks/' . $task->id, 'public');

                $fileRecord = File::create([
                    'name' => $file->getClientOriginalName(),
                    'file' => $path,
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'task_id' => $task->id,
                    'user_id' => $user->id,
                    'department_id' => $task->department_id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Файл успешно прикреплен',
                    'file' => $fileRecord
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Файл не был загружен'
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Ошибка при прикреплении файла: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при прикреплении файла'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $user = Auth::user();

        // Проверяем доступ к задаче
        if (!$this->canAccessTask($user, $task)) {
            abort(403, 'У вас нет доступа к этой задаче');
        }

        $task->load(['author', 'user', 'department', 'category', 'files', 'subtasks']);

        return view('frontend.tasks.show', compact('task', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $user = Auth::user();

        if (!$user->canViewAllCompanyTasks()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для редактирования задач'
            ], 403);
        }

        if ($task->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Задача не принадлежит вашей компании'
            ], 403);
        }

        // Загружаем все необходимые отношения
        $task->load(['author', 'user', 'department', 'category', 'files', 'subtasks']);

        return response()->json([
            'success' => true,
            'task' => $task,
            'departments' => Department::where('company_id', $user->company_id)->get(),
            'categories' => Category::where('company_id', $user->company_id)->get(),
            'users' => User::where('company_id', $user->company_id)->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $user = Auth::user();

        if (!$user->canViewAllCompanyTasks()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для редактирования задач'
            ], 403);
        }

        if ($task->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Задача не принадлежит вашей компании'
            ], 403);
        }

        // Валидация
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'category_id' => 'nullable|exists:categories,id',
            'user_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:низкий,средний,высокий,критический',
            'status' => 'required|in:назначена,не назначена,в работе,просрочена,на проверке,выполнена',
            'deadline' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Ошибки валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Проверяем, что отдел принадлежит компании
            $department = Department::find($request->department_id);
            if (!$department || $department->company_id !== $user->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Выбранный отдел не принадлежит вашей компании'
                ], 422);
            }

            // Проверяем исполнителя
            $oldUserId = $task->user_id;
            $newAssignedUser = null;

            if ($request->user_id) {
                $newAssignedUser = User::find($request->user_id);
                if (!$newAssignedUser || $newAssignedUser->company_id !== $user->company_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Выбранный исполнитель не работает в вашей компании'
                    ], 422);
                }
            }

            // Обновляем задачу
            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'category_id' => $request->category_id,
                'user_id' => $request->user_id,
                'priority' => $request->priority,
                'status' => $request->status,
                'deadline' => $request->deadline,
                'estimated_hours' => $request->estimated_hours,
                'actual_hours' => $request->actual_hours,
            ];

            // Если задача завершена, устанавливаем время завершения
            if ($request->status === 'выполнена' && $task->status !== 'выполнена') {
                $updateData['completed_at'] = now();
            }

            // Если задача больше не выполнена, сбрасываем время завершения
            if ($request->status !== 'выполнена' && $task->status === 'выполнена') {
                $updateData['completed_at'] = null;
            }

            $task->update($updateData);

            // Отправляем уведомление, если изменился исполнитель
            if ($newAssignedUser && $oldUserId !== $newAssignedUser->id) {
                try {
                    $newAssignedUser->notify(new TaskAssignedNotification($task));
                    \Log::info('Notification sent to new assigned user');
                } catch (\Exception $e) {
                    \Log::error('Notification error: ' . $e->getMessage());
                }
            }

            \Log::info('Task updated successfully', ['task_id' => $task->id, 'updated_fields' => array_keys($updateData)]);

            return response()->json([
                'success' => true,
                'message' => 'Задача успешно обновлена',
                'task' => $task->load(['department', 'category', 'user', 'author'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при обновлении задачи: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении задачи: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task)
    {
        $user = Auth::user();

        try {
            // Проверяем, что пользователь является автором задачи
            if (!$task->canBeDeletedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы можете удалять только свои задачи'
                ], 403);
            }

            // Проверяем, можно ли удалить задачу
            if (!$task->canBeDeleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Невозможно удалить задачу. Возможно, она уже выполнена или имеет подзадачи.'
                ], 422);
            }

            // Выполняем мягкое удаление с сохранением информации о том, кто удалил
            $task->update(['deleted_by' => $user->id]);
            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Задача успешно удалена'
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при удалении задачи: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении задачи'
            ], 500);
        }
    }

    /**
     * Check if user can access task
     */
    private function canAccessTask(User $user, Task $task): bool
    {
        // Руководители видят все задачи компании
        if ($user->canViewAllCompanyTasks()) {
            return $task->company_id === $user->company_id;
        }

        // Обычные пользователи видят только свои задачи и задачи своего отдела
        return $task->company_id === $user->company_id &&
            ($task->user_id === $user->id || $task->department_id === $user->department_id);
    }
}
