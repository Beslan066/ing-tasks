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

        // Предзагружаем роль для оптимизации
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        // Проверяем, является ли пользователь руководителем или менеджером
        $isManagerOrLeader = $user->isManager();

        // Если обычный сотрудник пытается зайти на админскую страницу - запрещаем
        if (!$isManagerOrLeader &&
            ($request->route()->getName() === 'tasks.admin' || $request->is('admin/tasks'))) {
            abort(403, 'У вас нет прав для доступа к панели руководителя');
        }

        // УБИРАЕМ автоматический редирект на админку для менеджеров
        // Руководители и менеджеры могут выбирать куда переходить

        return $next($request);
    }
}
