<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskCommentController extends Controller
{
    /**
     * Получить комментарии к задаче
     */
    public function index(Task $task)
    {
        $user = Auth::user();

        if (!$this->canViewComments($task, $user)) {
            return response()->json(['error' => 'Нет доступа к комментариям'], 403);
        }

        if ($task->is_personal) {
            return response()->json([
                'comments' => [],
                'message' => 'Сообщений к задаче нет',
                'can_comment' => false
            ]);
        }

        $comments = $task->comments()
            ->with(['user', 'replies.user'])
            ->paginate(20);

        return response()->json([
            'success' => true,
            'comments' => $comments,
            'can_comment' => $this->canComment($task, $user)
        ]);
    }

    /**
     * Создать комментарий
     */
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'comment' => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:task_comments,id'
        ]);

        $user = Auth::user();

        if (!$this->canComment($task, $user)) {
            return response()->json(['error' => 'У вас нет прав на комментирование'], 403);
        }

        if ($task->is_personal) {
            return response()->json(['error' => 'К личным задачам нельзя оставлять комментарии'], 403);
        }

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
            'parent_id' => $request->parent_id
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'message' => 'Комментарий добавлен'
        ]);
    }

    /**
     * Удалить комментарий
     */
    public function destroy(Task $task, TaskComment $comment)
    {
        if ($comment->task_id !== $task->id) {
            return response()->json(['error' => 'Комментарий не принадлежит этой задаче'], 404);
        }

        $user = Auth::user();

        if ($comment->user_id !== $user->id && !$user->isLeader()) {
            return response()->json(['error' => 'Нет прав на удаление комментария'], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Комментарий удален'
        ]);
    }

    /**
     * Обновить комментарий
     */
    public function update(Request $request, Task $task, TaskComment $comment)
    {
        if ($comment->task_id !== $task->id) {
            return response()->json(['error' => 'Комментарий не принадлежит этой задаче'], 404);
        }

        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'Нет прав на редактирование комментария'], 403);
        }

        $request->validate([
            'comment' => 'required|string|max:5000'
        ]);

        $comment->update([
            'comment' => $request->comment
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'message' => 'Комментарий обновлен'
        ]);
    }

    /**
     * Проверить, может ли пользователь видеть комментарии
     */
    private function canViewComments(Task $task, $user): bool
    {
        if ($task->is_personal) {
            return $task->author_id === $user->id || $task->user_id === $user->id;
        }

        if ($task->department_id) {
            return $user->isInDepartment($task->department_id);
        }

        return $task->company_id === $user->company_id;
    }

    /**
     * Проверить, может ли пользователь писать комментарии
     */
    private function canComment(Task $task, $user): bool
    {
        if ($task->is_personal) {
            return false;
        }

        if ($task->department_id) {
            return $user->isInDepartment($task->department_id);
        }

        return $task->company_id === $user->company_id;
    }
}
