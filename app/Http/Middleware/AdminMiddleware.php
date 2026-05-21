<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Здесь вы можете добавить свою логику проверки админа
        // Например, поле is_admin в таблице users
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403, 'Доступ запрещен');
        }

        return $next($request);
    }
}
