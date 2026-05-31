<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityFeedController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $query = Activity::forCompany($companyId)
            ->with(['user', 'subject'])
            ->latest();

        // Фильтр по типу действия
        if ($request->has('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        // Фильтр по пользователю
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $activities = $query->paginate(30);

        // Статистика для фильтров
        $actions = Activity::forCompany($companyId)
            ->select('action')
            ->distinct()
            ->pluck('action')
            ->map(function($action) {
                $labels = [
                    'task_created' => 'Создание задач',
                    'task_assigned' => 'Назначение задач',
                    'task_completed' => 'Выполнение задач',
                    'task_updated' => 'Обновление задач',
                    'task_deleted' => 'Удаление задач',
                    'task_rejected' => 'Отказы от задач',
                    'user_invited' => 'Приглашения',
                    'user_joined' => 'Присоединения',
                    'user_removed' => 'Удаление пользователей',
                    'file_uploaded' => 'Загрузка файлов',
                    'file_deleted' => 'Удаление файлов'
                ];
                return [
                    'value' => $action,
                    'label' => $labels[$action] ?? $action
                ];
            });

        $users = User::where('company_id', $companyId)
            ->whereIn('id', Activity::forCompany($companyId)->pluck('user_id')->unique())
            ->get();

        return view('frontend.activity.index', compact('activities', 'actions', 'users'));
    }

    public function show(Activity $activity)
    {
        $user = Auth::user();

        if ($activity->company_id !== $user->company_id) {
            abort(403);
        }

        $activity->load(['user', 'subject']);

        return view('frontend.activity.show', compact('activity'));
    }
}
