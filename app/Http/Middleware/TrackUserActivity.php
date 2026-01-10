<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Сначала выполняем запрос
        $response = $next($request);

        // Если пользователь авторизован
        if (Auth::check()) {
            $user = Auth::user();

            // Пропускаем определенные маршруты (API, ассеты и т.д.)
            if ($this->shouldTrack($request)) {
                $this->updateUserActivity($user);
            }
        }

        return $response;
    }

    /**
     * Определяем, нужно ли отслеживать активность для этого запроса
     */
    private function shouldTrack(Request $request): bool
    {
        $path = $request->path();

        // Исключаем маршруты, которые не должны обновлять активность
        $excludedPaths = [
            'api/',
            'broadcasting/',
            'horizon/',
            'telescope/',
            'storage/',
            'vendor/',
            'css/',
            'js/',
            'fonts/',
            'images/',
        ];

        foreach ($excludedPaths as $excluded) {
            if (str_starts_with($path, $excluded)) {
                return false;
            }
        }

        // Исключаем определенные методы
        if ($request->isMethod('options') || $request->isMethod('head')) {
            return false;
        }

        return true;
    }

    /**
     * Обновляем активность пользователя с кэшированием
     */
    private function updateUserActivity($user): void
    {
        $cacheKey = 'user_activity_' . $user->id;

        // Обновляем только если прошло больше 30 секунд с последнего обновления
        if (!Cache::has($cacheKey)) {
            try {
                // Используем update без событий для оптимизации
                \DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'last_activity_at' => now(),
                        'updated_at' => now(),
                    ]);

                // Обновляем объект пользователя в памяти
                $user->last_activity_at = now();

                // Кэшируем на 30 секунд
                Cache::put($cacheKey, true, 30);

            } catch (\Exception $e) {
                // Логируем ошибку, но не прерываем выполнение
                \Log::error('Failed to update user activity: ' . $e->getMessage());
            }
        }
    }
}
