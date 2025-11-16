<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagerMiddleware
{
    /**
     * Проверяет, является ли пользователь менеджером или руководителем
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем аутентификацию
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Проверяем, активен ли пользователь
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Ваш аккаунт деактивирован.'
            ]);
        }

        // Проверяем права доступа (менеджер или руководитель)
        if (!$user->isManager()) {
            // Для API запросов возвращаем JSON ошибку
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Доступ запрещен. Недостаточно прав.'
                ], 403);
            }

            // Для веб-запросов редирект с сообщением
            return redirect()->route('dashboard')
                ->with('error', 'У вас недостаточно прав для доступа к этой странице.');
        }

        return $next($request);
    }
}
