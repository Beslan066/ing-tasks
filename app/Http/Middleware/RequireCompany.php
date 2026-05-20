<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireCompany
{
    /**
     * Проверяет, что у пользователя есть компания
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Если пользователь не авторизован - пропускаем (Auth middleware сработает отдельно)
        if (!$user) {
            return $next($request);
        }

        // Проверяем, есть ли у пользователя компания
        if (!$user->company) {
            // Для API запросов возвращаем JSON ошибку
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'У вас нет компании. Пожалуйста, создайте компанию для доступа к этому разделу.',
                    'redirect' => route('no.companies')
                ], 403);
            }

            // Сохраняем intended URL для редиректа после создания компании
            session()->put('url.intended', $request->url());

            // Для веб-запросов редирект на страницу создания компании
            return redirect()->route('no.companies')
                ->with('warning', 'Для доступа к этому разделу необходимо создать компанию.');
        }

        return $next($request);
    }
}
