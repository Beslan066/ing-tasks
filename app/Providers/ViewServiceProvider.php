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
            $user = auth::user();

            if (!$user) {
                return $this->shareGuestData($view);
            }

            // Используем кэширование на время запроса
            $cacheKey = 'view_data_' . $user->id;

            if (Cache::has($cacheKey)) {
                $view->with(Cache::get($cacheKey));
                return;
            }

            $data = $this->getUserViewData($user);

            // Кэшируем на 5 минут
            Cache::put($cacheKey, $data, 300);

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
            'roles' => Role::all(),
            'isLeader' => false,
            'isManagerRole' => false,
            'currentUser' => null,
        ]);
    }

    private function getUserViewData($user)
    {
        // Предзагружаем данные
        $user->load(['company', 'role']);

        // ====== КОМПАНИИ ======
        $ownedCompanies = $this->getUserCompanies($user);

        // ====== ОТДЕЛЫ ======
        $departments = $this->getUserDepartments($user);

        // ====== КАТЕГОРИИ ======
        $categories = $this->getUserCategories($user);

        // ====== КОМАНДА ======
        list($team, $assignableUsers) = $this->getUserTeamData($user);

        return [
            'departments' => $departments,
            'categories' => $categories,
            'ownedCompanies' => $ownedCompanies,
            'assignableUsers' => $assignableUsers,
            'team' => $team,
            'roles' => Role::all(),
            'isLeader' => $user->isLeader(),
            'isManagerRole' => $user->isManagerRole(),
            'currentUser' => $user,
        ];
    }

    private function getUserCompanies($user)
    {
        $ownedCompanies = $user->ownedCompanies()->orderBy('id', 'desc')->get();

        // Добавляем текущую компанию если пользователь ее владелец
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

        // ВСЕ пользователи компании (для отображения)
        $team = User::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->with('department', 'role')
            ->get();

        // Пользователи, которым МОЖНО назначать задачи
        $assignableUsers = $user->getAssignableUsers();

        return [$team, $assignableUsers];
    }
}
