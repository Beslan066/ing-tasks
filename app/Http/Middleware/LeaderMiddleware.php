<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LeaderMiddleware
{
    /**
     * Проверяет, является ли пользователь руководителем любого уровня
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

        // Проверяем права доступа (только руководители)
        if (!$user->isLeader()) {
            // Для API запросов возвращаем JSON ошибку
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Доступ запрещен. Только для руководителей.'
                ], 403);
            }

            // Для веб-запросов редирект с сообщением
            return redirect()->route('dashboard')
                ->with('error', 'Доступ разрешен только руководителям.');
        }

        return $next($request);
    }
}
