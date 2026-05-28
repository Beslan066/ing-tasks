<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOnlineSession;
use App\Models\UserSession;
use App\Models\UserVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserTrackingController extends Controller
{
    public function index()
    {
        try {
            // Загружаем пользователей с ОБОИМИ типами сессий
            $users = User::with([
                'sessions' => function($query) {
                    $query->orderBy('last_activity', 'desc');
                },
                'onlineSessions' => function($query) {
                    $query->whereNull('logout_at')
                        ->orderBy('last_activity_at', 'desc');
                }
            ])->get();

            // Базовая статистика
            $totalUsers = User::count();
            $newUsersCount = User::where('created_at', '>=', now()->subWeek())->count();

            // Активные онлайн пользователи (за последние 5 минут)
            $activeSessions = UserOnlineSession::whereNull('logout_at')
                ->where('last_activity_at', '>=', now()->subMinutes(5))
                ->count();

            // Количество уникальных онлайн пользователей
            $onlineUsersCount = UserOnlineSession::whereNull('logout_at')
                ->where('last_activity_at', '>=', now()->subMinutes(5))
                ->distinct('user_id')
                ->count('user_id');

            // Процент онлайн
            $activePercentage = $totalUsers > 0 ? round(($onlineUsersCount / $totalUsers) * 100) : 0;

            // Уникальные устройства за сегодня
            $uniqueDevices = UserSession::whereDate('last_activity', today())
                ->distinct('device_type')
                ->count('device_type');

            // Страны из UserSession
            $countriesCount = UserSession::whereNotNull('country')
                ->whereDate('last_activity', today())
                ->distinct('country')
                ->count('country');

            // Для фильтра стран используем UserSession
            $uniqueCountries = UserSession::whereNotNull('country')
                ->distinct()
                ->pluck('country')
                ->toArray();

            return view('admin.users-tracking', compact(
                'users',
                'totalUsers',
                'newUsersCount',
                'activeSessions',
                'onlineUsersCount',
                'activePercentage',
                'uniqueDevices',
                'countriesCount',
                'uniqueCountries'
            ));

        } catch (\Exception $e) {
            Log::error('UserTrackingController index error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Возвращаем пустые данные в случае ошибки
            return view('admin.users-tracking', [
                'users' => collect(),
                'totalUsers' => 0,
                'newUsersCount' => 0,
                'activeSessions' => 0,
                'onlineUsersCount' => 0,
                'activePercentage' => 0,
                'uniqueDevices' => 0,
                'countriesCount' => 0,
                'uniqueCountries' => [],
            ]);
        }
    }

    public function show(User $user)
    {
        try {
            $sessions = $user->onlineSessions()->orderBy('login_at', 'desc')->get();

            return view('admin.user-details', compact('user', 'sessions'));

        } catch (\Exception $e) {
            Log::error('UserTrackingController show error: ' . $e->getMessage());
            abort(404, 'Пользователь не найден');
        }
    }

    public function map()
    {
        try {
            // Берем данные из UserSession (где есть геолокация)
            $sessions = UserSession::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with('user')
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(function($session) {
                    // Добавляем информацию о пользователе
                    return [
                        'id' => $session->id,
                        'user_id' => $session->user_id,
                        'user' => $session->user ? [
                            'id' => $session->user->id,
                            'name' => $session->user->name,
                            'email' => $session->user->email,
                            'avatar' => $session->user->avatar,
                        ] : null,
                        'ip_address' => $session->ip_address,
                        'latitude' => $session->latitude,
                        'longitude' => $session->longitude,
                        'city' => $session->city,
                        'country' => $session->country,
                        'address' => $session->address,
                        'device_type' => $session->device_type,
                        'browser' => $session->browser,
                        'os' => $session->os,
                        'last_activity' => $session->last_activity,
                        'is_current' => $session->is_current,
                    ];
                });

            return view('admin.users-map', compact('sessions'));

        } catch (\Exception $e) {
            Log::error('UserTrackingController map error: ' . $e->getMessage());
            return view('admin.users-map', ['sessions' => collect()]);
        }
    }

    public function getOnlineUsers()
    {
        try {
            $onlineUsers = User::whereHas('onlineSessions', function($query) {
                $query->whereNull('logout_at')
                    ->where('last_activity_at', '>=', now()->subMinutes(5));
            })->with('onlineSessions')->get();

            return response()->json([
                'success' => true,
                'count' => $onlineUsers->count(),
                'users' => $onlineUsers->map(function($user) {
                    $session = $user->onlineSessions->first();
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar' => $user->avatar,
                        'last_activity' => $session ? $session->last_activity_at->diffForHumans() : null
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('getOnlineUsers error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'count' => 0,
                'users' => []
            ]);
        }
    }

    public function deleteSession($sessionId)
    {
        try {
            $session = UserOnlineSession::findOrFail($sessionId);
            $session->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('deleteSession error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function clearSessions($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->onlineSessions()->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('clearSessions error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function forceLogout($userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Завершаем все активные сессии
            UserOnlineSession::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->update([
                    'logout_at' => now(),
                    'last_activity_at' => now()
                ]);

            // Опционально: удаляем все сессии Laravel
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('forceLogout error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
