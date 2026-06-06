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
use App\Models\TaskComment;
use App\Notifications\TaskAssignedNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $categories = Category::where('company_id', $user->company_id)->get();

        $tasksByStatus = [
            'new' => $tasks->where('status', 'назначена'),
            'in_progress' => $tasks->where('status', 'в работе'),
            'review' => $tasks->where('status', 'на проверке'),
            'done' => $tasks->where('status', 'выполнена'),
        ];

        $stats = [
            'new' => $tasksByStatus['new']->count(),
            'in_progress' => $tasksByStatus['in_progress']->count(),
            'review' => $tasksByStatus['review']->count(),
            'done' => $tasksByStatus['done']->count(),
        ];

        return view('frontend.tasks.index', compact('tasks', 'user', 'tasksByStatus', 'stats', 'categories'));
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
        $assignableUsers = User::where('company_id', $user->company_id)->get();

        return view('frontend.task.create', compact('departments', 'categories', 'assignableUsers', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'category_id' => 'nullable|exists:categories,id',
            'user_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:низкий,средний,высокий,критический',
            'status' => 'required|in:назначена,не назначена,в работе,просрочена,на проверке,выполнена',
            'deadline' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'selected_file_ids' => 'nullable|array',
            'selected_file_ids.*' => 'exists:files,id',
            'new_files.*' => 'nullable|file|max:10240',
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
            $department = Department::find($request->department_id);
            if (!$department || $department->company_id !== $user->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Выбранный отдел не принадлежит вашей компании'
                ], 422);
            }

            $assignedUser = null;
            if ($request->user_id) {
                $assignedUser = User::find($request->user_id);
                if (!$assignedUser || $assignedUser->company_id !== $user->company_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Выбранный исполнитель не работает в вашей компании'
                    ], 422);
                }
            }

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

            $task = Task::create($taskData);

            if ($request->has('selected_file_ids')) {
                $selectedFiles = File::whereIn('id', $request->selected_file_ids)
                    ->where('company_id', $user->company_id)
                    ->get();
                foreach ($selectedFiles as $file) {
                    if (method_exists($task, 'files')) {
                        $task->files()->attach($file->id);
                    }
                }
            }

            if ($request->hasFile('new_files')) {
                foreach ($request->file('new_files') as $file) {
                    $path = $file->store("tasks/{$task->id}", 'public');
                    $fileRecord = File::create([
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'file_path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'extension' => $file->getClientOriginalExtension(),
                        'uploaded_by' => $user->id,
                        'company_id' => $user->company_id,
                        'department_id' => $task->department_id,
                        'disk' => 'public',
                        'folder' => 'tasks',
                        'is_public' => false,
                    ]);
                    if (method_exists($task, 'files')) {
                        $task->files()->attach($fileRecord->id);
                    }
                }
            }

            ActivityLogger::taskCreated($task, $user);

            if ($request->user_id && $request->user_id != $user->id) {
                $assignedUser = User::find($request->user_id);
                ActivityLogger::taskAssigned($task, $assignedUser, $user);
            }

            if ($assignedUser && $task->user_id) {
                try {
                    $assignedUser->notify(new TaskAssignedNotification($task));
                } catch (\Exception $e) {
                    \Log::error('Notification error: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Задача успешно создана',
                'task' => $task->load(['department', 'category', 'user', 'author', 'files'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in task store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании задачи: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Отображение задачи с комментариями для модального окна
     */
    /**
     * Отображение задачи с комментариями для модального окна
     */


    public function view(Task $task)
    {
        \Log::info('=== START view method for task ID: ' . $task->id . ', status: ' . $task->status . ' ===');

        try {
            $user = Auth::user();

            if (!$this->canAccessTask($user, $task)) {
                // Возвращаем HTML с сообщением об ошибке и статусом 403
                return response()->make(
                    '<div class="p-6 text-center">
                    <i class="fas fa-lock text-3xl text-red-500 mb-3"></i>
                    <p class="text-gray-700">У вас нет доступа к этой задаче</p>
                    <button onclick="closeTaskViewModal()" class="mt-4 px-4 py-2 bg-gray-500 text-white rounded-lg">Закрыть</button>
                </div>',
                    403
                );
            }

            $task->updateOverdueStatus();

            // Загружаем связи
            $task->load(['author', 'user', 'category']);

            // Отдел загружаем даже если NULL (withDefault сработает)
            $task->load('department');

            $files = $task->files()->get();

            $comments = null;
            $canComment = false;

            if (!$task->is_personal) {
                try {
                    $comments = $task->comments()->with(['user', 'replies.user'])->paginate(20);
                    $canComment = $task->canUserComment($user);
                    \Log::info('canComment result: ' . ($canComment ? 'true' : 'false'));
                } catch (\Exception $e) {
                    \Log::error('Error loading comments: ' . $e->getMessage());
                    $comments = null;
                }
            }

            $subtasks = $task->subtasks()->with(['user', 'author'])->get();
            $currentUser = Auth::user();

            // Всегда возвращаем только контент модального окна
            return view('partials.modal.task.modal_content', compact(
                'task', 'comments', 'files', 'subtasks', 'currentUser', 'canComment'
            ));

        } catch (\Exception $e) {
            \Log::error('!!! CRITICAL ERROR for task ' . $task->id . ': ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->make(
                '<div class="p-6 text-center">
                <i class="fas fa-exclamation-triangle text-3xl text-red-500 mb-3"></i>
                <p class="text-gray-700">Ошибка при загрузке задачи: ' . $e->getMessage() . '</p>
                <button onclick="closeTaskViewModal()" class="mt-4 px-4 py-2 bg-gray-500 text-white rounded-lg">Закрыть</button>
            </div>',
                500
            );
        }
    }

    /**
     * Get task data for editing
     */
    public function getTask(Task $task)
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
            if ($task->user_id !== null || $task->status !== 'не назначена') {
                return response()->json([
                    'success' => false,
                    'message' => 'Задача уже назначена или недоступна'
                ], 422);
            }

            if (!$user->isInDepartment($task->department_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы не можете взять задачу из другого отдела'
                ], 422);
            }

            $task->update([
                'user_id' => $user->id,
                'status' => 'в работе'
            ]);

            ActivityLogger::taskAssigned($task, $user, $user);

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

    public function updateTaskStatus(Request $request, Task $task)
    {
        try {
            $user = Auth::user();
            $oldStatus = $task->status;

            if ($task->user_id !== $user->id && $task->author_id !== $user->id && !$user->canViewAllCompanyTasks()) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав на изменение этой задачи'
                ], 403);
            }

            $validator = \Validator::make($request->all(), [
                'status' => 'required|in:назначена,в работе,на проверке,выполнена'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недопустимый статус'
                ], 422);
            }

            $updateData = ['status' => $request->status];

            if ($request->status === 'на проверке' && $request->has('actual_hours')) {
                $updateData['actual_hours'] = $request->actual_hours;
            }

            if ($request->status === 'выполнена') {
                $updateData['completed_at'] = now();
            } elseif ($request->status === 'назначена' && $task->completed_at) {
                $updateData['completed_at'] = null;
            }

            $task->update($updateData);

            if ($oldStatus != $request->status) {
                ActivityLogger::taskStatusChanged($task, $oldStatus, $request->status, $user);
            }

            if ($request->status === 'выполнена' && $oldStatus !== 'выполнена') {
                ActivityLogger::taskCompleted($task, $user);
            }

            return response()->json([
                'success' => true,
                'message' => 'Статус задачи успешно обновлен',
                'task' => $task->load(['department', 'category', 'author'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in updateTaskStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении статуса задачи: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectTask(Request $request, Task $task)
    {
        $user = Auth::user();

        try {
            if ($task->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы не являетесь исполнителем этой задачи'
                ], 403);
            }

            $request->validate([
                'reason' => 'required|string|max:1000'
            ]);

            TaskRejection::create([
                'reason' => $request->reason,
                'task_id' => $task->id,
                'user_id' => $user->id,
                'company_id' => $user->company_id
            ]);

            $task->update([
                'user_id' => null,
                'status' => 'не назначена'
            ]);

            ActivityLogger::taskRejected($task, $user, $request->reason);

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

    public function attachFile(Request $request, Task $task)
    {
        $user = Auth::user();

        try {
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

        $task->load(['author', 'user', 'department', 'category', 'files']);

        return response()->json([
            'success' => true,
            'task' => $task,
            'departments' => Department::where('company_id', $user->company_id)->get(),
            'categories' => Category::where('company_id', $user->company_id)->get(),
            'users' => User::where('company_id', $user->company_id)->get()
        ]);
    }

    /**
     * Update task
     */
    public function update(Request $request, Task $task)
    {
        $user = Auth::user();
        $oldUserId = $task->user_id;
        $oldStatus = $task->status;
        $oldDepartmentId = $task->department_id;
        $oldPriority = $task->priority;
        $oldDeadline = $task->deadline;
        $oldEstimatedHours = $task->estimated_hours;
        $oldActualHours = $task->actual_hours;
        $oldName = $task->name;
        $oldDescription = $task->description;
        $oldCategoryId = $task->category_id;

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
            return response()->json([
                'success' => false,
                'message' => 'Ошибки валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $department = Department::find($request->department_id);
            if (!$department || $department->company_id !== $user->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Выбранный отдел не принадлежит вашей компании'
                ], 422);
            }

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

            if ($request->status === 'выполнена' && $task->status !== 'выполнена') {
                $updateData['completed_at'] = now();
            }

            if ($request->status !== 'выполнена' && $task->status === 'выполнена') {
                $updateData['completed_at'] = null;
            }

            $task->update($updateData);

            $selectedFileIds = [];

            if ($request->has('selected_files')) {
                $selectedFilesInput = $request->input('selected_files');
                if (is_string($selectedFilesInput)) {
                    $decoded = json_decode($selectedFilesInput, true);
                    if (is_array($decoded)) {
                        $selectedFileIds = $decoded;
                    }
                } elseif (is_array($selectedFilesInput)) {
                    $selectedFileIds = $selectedFilesInput;
                }
            }

            $selectedFileIds = array_unique(array_filter($selectedFileIds));

            if (!empty($selectedFileIds)) {
                $task->files()->sync($selectedFileIds);
            } else {
                $task->files()->detach();
            }

            if ($newAssignedUser && $oldUserId !== $newAssignedUser->id) {
                try {
                    $newAssignedUser->notify(new TaskAssignedNotification($task));
                } catch (\Exception $e) {
                    \Log::error('Notification error: ' . $e->getMessage());
                }
            }

            $changes = [];

            if ($oldName != $request->name) {
                $changes[] = "название: '{$oldName}' → '{$request->name}'";
            }
            if ($oldStatus != $request->status) {
                $changes[] = "статус: '{$oldStatus}' → '{$request->status}'";
            }
            if ($oldUserId != $request->user_id) {
                $oldUserName = $oldUserId ? User::find($oldUserId)?->name : 'не назначен';
                $newUserName = $request->user_id ? User::find($request->user_id)?->name : 'не назначен';
                $changes[] = "исполнитель: '{$oldUserName}' → '{$newUserName}'";
            }
            if ($oldDepartmentId != $request->department_id) {
                $oldDeptName = Department::find($oldDepartmentId)?->name ?? 'не указан';
                $newDeptName = Department::find($request->department_id)?->name ?? 'не указан';
                $changes[] = "отдел: '{$oldDeptName}' → '{$newDeptName}'";
            }
            if ($oldCategoryId != $request->category_id) {
                $oldCatName = Category::find($oldCategoryId)?->name ?? 'не указана';
                $newCatName = Category::find($request->category_id)?->name ?? 'не указана';
                $changes[] = "категория: '{$oldCatName}' → '{$newCatName}'";
            }
            if ($oldPriority != $request->priority) {
                $changes[] = "приоритет: '{$oldPriority}' → '{$request->priority}'";
            }
            if ($oldDeadline != $request->deadline) {
                $oldDeadlineStr = $oldDeadline ? date('d.m.Y H:i', strtotime($oldDeadline)) : 'не указан';
                $newDeadlineStr = $request->deadline ? date('d.m.Y H:i', strtotime($request->deadline)) : 'не указан';
                $changes[] = "дедлайн: '{$oldDeadlineStr}' → '{$newDeadlineStr}'";
            }
            if ($oldEstimatedHours != $request->estimated_hours) {
                $changes[] = "планируемые часы: '{$oldEstimatedHours}' → '{$request->estimated_hours}'";
            }
            if ($oldActualHours != $request->actual_hours) {
                $changes[] = "фактические часы: '{$oldActualHours}' → '{$request->actual_hours}'";
            }

            if (!empty($changes)) {
                $changesText = implode(', ', $changes);
                ActivityLogger::taskUpdated($task, $user, $changesText);
            }

            if ($oldUserId != $request->user_id && $request->user_id) {
                $newUser = User::find($request->user_id);
                ActivityLogger::taskAssigned($task, $newUser, $user);
            }

            if ($oldStatus != $request->status) {
                ActivityLogger::taskStatusChanged($task, $oldStatus, $request->status, $user);
            }

            DB::commit();

            $task->load(['department', 'category', 'user', 'author', 'files']);

            return response()->json([
                'success' => true,
                'message' => 'Задача успешно обновлена',
                'task' => $task
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating task: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении задачи: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add files to task
     */
    public function addFiles(Request $request, Task $task)
    {
        $user = Auth::user();

        if (!$user->canViewAllCompanyTasks()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для добавления файлов'
            ], 403);
        }

        if ($task->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Задача не принадлежит вашей компании'
            ], 403);
        }

        $request->validate([
            'files.*' => 'required|file|max:10240',
        ]);

        try {
            $uploadedFiles = [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store("tasks/{$task->id}", 'public');

                    $fileRecord = File::create([
                        'name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'path' => $path,
                        'file_size' => $file->getSize(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'extension' => $file->getClientOriginalExtension(),
                        'uploaded_by' => $user->id,
                        'company_id' => $user->company_id,
                        'department_id' => $task->department_id,
                        'disk' => 'public',
                        'folder' => 'tasks',
                        'is_public' => false,
                    ]);

                    $task->files()->attach($fileRecord->id);
                    $uploadedFiles[] = $fileRecord;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Файлы успешно добавлены',
                'files' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при добавлении файлов: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении файлов: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete file from task
     */
    public function deleteFile($fileId)
    {
        $user = Auth::user();

        try {
            $file = File::findOrFail($fileId);
            $task = $file->tasks()->first();
            if ($task && !$user->canViewAllCompanyTasks()) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав для удаления этого файла'
                ], 403);
            }

            if ($task) {
                $task->files()->detach($fileId);
            }

            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            return response()->json([
                'success' => true,
                'message' => 'Файл успешно удален'
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при удалении файла: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении файла'
            ], 500);
        }
    }

    /**
     * Return task to work (for admin)
     */
    public function returnToWork(Request $request, Task $task)
    {
        $user = Auth::user();

        if (!$user->canViewAllCompanyTasks()) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для этого действия'
            ], 403);
        }

        if ($task->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Задача не принадлежит вашей компании'
            ], 403);
        }

        $request->validate([
            'comment' => 'nullable|string|max:1000'
        ]);

        try {
            TaskRejection::create([
                'reason' => $request->comment ?: 'Возврат на доработку руководителем',
                'task_id' => $task->id,
                'user_id' => $user->id,
                'company_id' => $user->company_id
            ]);

            $task->update([
                'status' => 'в работе'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Задача возвращена на доработку'
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при возврате задачи: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при возврате задачи'
            ], 500);
        }
    }

    public function destroy(Request $request, Task $task)
    {
        $user = Auth::user();

        try {
            if (!$task->canBeDeletedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы можете удалять только свои задачи'
                ], 403);
            }

            if (!$task->canBeDeleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Невозможно удалить задачу'
                ], 422);
            }

            $task->update(['deleted_by' => $user->id]);
            $task->delete();

            ActivityLogger::taskDeleted($task, $user);

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
        if ($user->canViewAllCompanyTasks()) {
            return $task->company_id === $user->company_id;
        }

        // Проверяем, есть ли у задачи отдел
        $isInDepartment = false;
        if ($task->department_id) {
            $isInDepartment = $user->isInDepartment($task->department_id);
        }

        return $task->company_id === $user->company_id &&
            ($task->user_id === $user->id || $isInDepartment);
    }

    public function storePersonal(Request $request)
    {
        $user = Auth::user();

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'priority' => 'required|in:низкий,средний,высокий,критический',
            'deadline' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'files.*' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибки валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->category_id) {
                $category = Category::find($request->category_id);
                if (!$category || $category->company_id !== $user->company_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Выбранная категория не доступна'
                    ], 422);
                }
            }

            $primaryDepartment = $user->departments()->first();
            $departmentId = $primaryDepartment ? $primaryDepartment->id : null;

            $taskData = [
                'name' => $request->name,
                'description' => $request->description,
                'department_id' => $departmentId,
                'category_id' => $request->category_id,
                'user_id' => $user->id,
                'priority' => $request->priority,
                'status' => 'назначена',
                'deadline' => $request->deadline,
                'estimated_hours' => $request->estimated_hours,
                'company_id' => $user->company_id,
                'author_id' => $user->id,
                'is_personal' => true,
            ];

            $task = Task::create($taskData);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('tasks/' . $task->id, 'public');
                    File::create([
                        'name' => $file->getClientOriginalName(),
                        'file' => $path,
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                        'department_id' => $departmentId,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Личная задача успешно создана',
                'task' => $task->load(['department', 'category'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creating personal task: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании задачи: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFiles(Request $request)
    {
        $user = Auth::user();

        $files = File::where('company_id', $user->company_id)
            ->select('id', 'name', 'size', 'extension', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->name,
                    'size' => $file->size,
                    'extension' => $file->extension,
                    'created_at' => $file->created_at->toDateTimeString(),
                    'formatted_size' => $this->formatFileSize($file->size),
                ];
            });

        return response()->json($files);
    }

    private function formatFileSize($bytes)
    {
        if ($bytes == 0) return "0 Bytes";
        $k = 1024;
        $sizes = ["Bytes", "KB", "MB", "GB"];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . " " . $sizes[$i];
    }
}
