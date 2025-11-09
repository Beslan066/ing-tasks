<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        // Проверяем, является ли пользователь руководителем
        if ($user->isManager()) {
            // Если это главная страница, перенаправляем на админскую версию
            if ($request->route()->getName() === 'welcome' || $request->is('/')) {
                return redirect()->route('tasks.admin');
            }
        } else {
            // Если сотрудник пытается зайти на админскую страницу - ЗАПРЕЩАЕМ доступ
            if ($request->route()->getName() === 'tasks.admin' || $request->is('admin/tasks')) {
                abort(403, 'У вас нет прав для доступа к панели руководителя');
            }
        }

        return $next($request);
    }
}
