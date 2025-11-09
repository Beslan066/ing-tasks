<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
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
            $user = auth()->user();
            $roles = Role::all();

            if ($user) {
                // Отделы пользователя
                $departments = $user->departments()
                    ->with('company')
                    ->orderBy('id', 'desc')
                    ->get();

                // Категории через отделы пользователя
                $categories = collect();
                if ($departments->isNotEmpty()) {
                    $companyIds = $departments->pluck('company_id')->unique()->filter();
                    if ($companyIds->isNotEmpty()) {
                        $categories = Category::whereIn('company_id', $companyIds)->orderBy('id', 'desc')
                            ->get();
                    }
                }

                // Компании пользователя
                $ownedCompanies = $user->ownedCompanies()->orderBy('id', 'desc')
                    ->get();

                $team = User::query()->where('company_id', $user->company_id)->get();

                // ВРЕМЕННО: Получаем пользователей через отделы (пока нет company_id)
                $assignableUsers = collect();
                if ($departments->isNotEmpty()) {
                    // Получаем всех пользователей, которые состоят в тех же отделах
                    $departmentIds = $departments->pluck('id');
                    $assignableUsers = User::query()
                        ->whereIn('department_id', $departmentIds)
                        ->where('id', '!=', $user->id) // исключаем текущего пользователя
                        ->get();
                }

                $view->with([
                    'departments' => $departments,
                    'categories' => $categories,
                    'ownedCompanies' => $ownedCompanies,
                    'assignableUsers' => $assignableUsers,
                    'roles' => $roles,
                    'team' => $team,
                ]);
            } else {
                $view->with([
                    'departments' => collect(),
                    'categories' => collect(),
                    'ownedCompanies' => collect(),
                    'assignableUsers' => collect(),
                ]);
            }
        });
    }
}
