<?php

namespace App\Http\Middleware;

use App\Models\UserOnlineSession;
use App\Models\UserVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check()) {
            $user = Auth::user();

            if ($this->shouldTrack($request)) {
                $this->updateUserActivity($user);
                $this->trackSession($user);

                // Только для GET запросов и не AJAX
                if ($request->isMethod('get') && !$request->ajax() && !$request->wantsJson()) {
                    $this->trackVisit($request, $user);
                }
            }
        }

        return $response;
    }

    private function shouldTrack(Request $request): bool
    {
        $path = $request->path();

        // Исключаем маршруты, которые не должны трекаться
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
            'livewire/',
            'debugbar/',
            'team/user/*/detailed-stats',  // Исключаем API статистики
            'team/user/*/tasks',           // Исключаем API задач
            'team/user/*/export-stats',    // Исключаем экспорт
            'team/departments/list',       // Исключаем API отделов
            'team/invitations/search',     // Исключаем поиск приглашений
            'chat/api/',                   // Исключаем API чата
            'get-online-users',            // Исключаем API онлайн пользователей
            'update-activity',             // Исключаем обновление активности
        ];

        foreach ($excludedPaths as $excluded) {
            if ($request->is($excluded)) {
                return false;
            }
        }

        // Исключаем AJAX запросы
        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }

        // Исключаем определенные методы
        if ($request->isMethod('options') || $request->isMethod('head')) {
            return false;
        }

        // Исключаем POST, PUT, PATCH, DELETE запросы (только GET для просмотров)
        if (!$request->isMethod('get')) {
            return false;
        }

        return true;
    }

    private function updateUserActivity($user): void
    {
        $cacheKey = 'user_activity_' . $user->id;

        if (!Cache::has($cacheKey)) {
            try {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'last_activity_at' => now(),
                        'updated_at' => now(),
                    ]);

                $user->last_activity_at = now();
                Cache::put($cacheKey, true, 30);

            } catch (\Exception $e) {
                \Log::error('Failed to update user activity: ' . $e->getMessage());
            }
        }
    }

    private function trackSession($user): void
    {
        try {
            $today = now()->toDateString();
            $sessionId = session()->getId();

            $session = UserOnlineSession::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->whereNull('logout_at')
                ->first();

            if (!$session) {
                UserOnlineSession::create([
                    'user_id' => $user->id,
                    'login_at' => now(),
                    'session_id' => $sessionId,
                    'date' => $today,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'last_activity_at' => now(),
                ]);
            } else {
                $session->update([
                    'last_activity_at' => now(),
                ]);

                if ($session->ip_address !== request()->ip()) {
                    $session->update(['ip_address' => request()->ip()]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to track session: ' . $e->getMessage());
        }
    }

    private function trackVisit(Request $request, $user): void
    {
        try {
            $today = now()->toDateString();

            $visit = UserVisit::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $today,
                ],
                [
                    'first_visit_at' => now(),
                    'last_visit_at' => now(),
                    'page_views' => 0,
                    'total_time_seconds' => 0,
                ]
            );

            $visit->increment('page_views');
            $visit->update(['last_visit_at' => now()]);

        } catch (\Exception $e) {
            \Log::error('Failed to track visit: ' . $e->getMessage());
        }
    }
}
