<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */

    public function boot()
    {
        View::composer('*', function ($view) {
            $user = Auth::user();

            if (!$user) {
                return $this->shareGuestData($view);
            }

            // Для онлайн пользователей кэшируем на 15 секунд,
            $cacheKey = 'view_data_' . $user->id;
            $cacheTime = 15; // секунды

            if (Cache::has($cacheKey)) {
                $view->with(Cache::get($cacheKey));
                return;
            }

            $data = $this->getUserViewData($user);

            // Кэшируем на 15 секунд для быстрого обновления
            Cache::put($cacheKey, $data, $cacheTime);

            $view->with($data);
        });
    }

    private function shareGuestData($view)
    {
        $view->with([
            'departments' => collect(),
            'categories' => collect(),
            'ownedCompanies' => collect(),
            'assignableUsers' => collect(),
            'team' => collect(),
            'onlineUsers' => collect(),
            'onlineUsersCount' => 0,
            'roles' => Role::all(),
            'isLeader' => false,
            'isManagerRole' => false,
            'currentUser' => null,
        ]);
    }

    private function getUserViewData($user)
    {
        // Предзагружаем данные
        $user->load(['company', 'role', 'departments']); // ← ДОБАВИЛ departments

        // ====== КОМПАНИИ ======
        $ownedCompanies = $this->getUserCompanies($user);

        // ====== ОТДЕЛЫ ======
        $departments = $this->getUserDepartments($user);

        // ====== КАТЕГОРИИ ======
        $categories = $this->getUserCategories($user);

        // ====== КОМАНДА ======
        list($team, $assignableUsers) = $this->getUserTeamData($user);

        // ====== ОНЛАЙН ПОЛЬЗОВАТЕЛИ ======
        $onlineUsers = $this->getOnlineUsers($user);

        // ====== ПОЧТА ======
        $unreadEmailsCount = $this->getUnreadEmailsCount($user);

        // Получаем общее количество онлайн пользователей
        $onlineUsersCount = $this->getOnlineUsersCount($user);

        return [
            'departments' => $departments,
            'categories' => $categories,
            'ownedCompanies' => $ownedCompanies,
            'assignableUsers' => $assignableUsers,
            'team' => $this->prepareTeamData($team), // Обрабатываем данные команды
            'unread_emails_count' => $unreadEmailsCount,
            'has_email_access' => $user->hasPermission('access_email'),
            'onlineUsers' => $onlineUsers,
            'onlineUsersCount' => $onlineUsersCount,
            'roles' => Role::all(),
            'isLeader' => $user->isLeader(),
            'isManagerRole' => $user->isManagerRole(),
            'currentUser' => $user,
        ];
    }

    private function prepareTeamData($team)
    {
        return $team->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->name,
                'initials' => $this->generateInitials($member->name),
                'color' => $this->generateColorFromName($member->name),
                'is_online' => $member->isOnline(),
                'last_activity_text' => $member->getLastActivityText(),
                'avatar_url' => $member->avatar_url,
                'departments' => $member->departments,
                'role' => $member->role,
            ];
        });
    }

    private function getOnlineUsersCount($user)
    {
        if (!$user->company_id) {
            return 0;
        }

        return User::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->whereNotNull('last_activity_at')
            ->where('last_activity_at', '>=', now()->subMinutes(5))
            ->count();
    }

    private function getUserCompanies($user)
    {
        $ownedCompanies = $user->ownedCompanies()->orderBy('id', 'desc')->get();

        if ($user->company && $user->company->user_id === $user->id) {
            $ownedCompanies = $ownedCompanies->push($user->company)->unique('id');
        }

        return $ownedCompanies;
    }

    private function getUserDepartments($user)
    {
        if ($user->company_id) {
            return Department::where('company_id', $user->company_id)
                ->with('company')
                ->orderBy('id', 'desc')
                ->get();
        }

        return $user->departments()
            ->with('company')
            ->orderBy('id', 'desc')
            ->get();
    }

    private function getUserCategories($user)
    {
        if ($user->company_id) {
            return Category::where('company_id', $user->company_id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return collect();
    }

    private function getUserTeamData($user)
    {
        if (!$user->company_id) {
            return [collect(), collect()];
        }

        $team = User::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->with('departments', 'role')
            ->get();

        $assignableUsers = $user->getAssignableUsers();

        return [$team, $assignableUsers];
    }

    private function getOnlineUsers($user)
    {
        if (!$user->company_id) {
            return collect();
        }

        $onlineUsers = User::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->whereNotNull('last_activity_at')
            ->where('last_activity_at', '>=', now()->subMinutes(5))
            ->orderBy('last_activity_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                $initials = $this->generateInitials($user->name);
                $color = $this->generateColorFromName($user->name);

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'initials' => $initials,
                    'color' => $color,
                    'is_online' => true,
                    'avatar_url' => $user->avatar_url,
                    'last_activity_at' => $user->last_activity_at,
                    'last_activity_text' => $user->getLastActivityText(),
                ];
            });

        return $onlineUsers;
    }

    public function generateInitials(string $name): string
    {
        $words = explode(' ', trim($name));
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
            }

            if (mb_strlen($initials, 'UTF-8') >= 2) {
                break;
            }
        }

        return $initials ?: '??';
    }

    private function generateColorFromName(string $name): string
    {
        $colors = [
            'bg-blue-500',
            'bg-purple-500',
            'bg-red-500',
            'bg-yellow-500',
            'bg-green-500',
            'bg-indigo-500',
            'bg-pink-500',
            'bg-teal-500',
            'bg-orange-500',
            'bg-cyan-500',
        ];

        $hash = crc32($name);
        $index = abs($hash) % count($colors);

        return $colors[$index];
    }

    private function getUnreadEmailsCount($user)
    {
        if (!$user->company_id) {
            return 0;
        }

        // Получаем общее количество непрочитанных писем из всех отделов пользователя
        $totalUnread = 0;
        foreach ($user->departments as $department) {
            $totalUnread += $department->unread_emails_count ?? 0;
        }

        return $totalUnread;
    }
}
