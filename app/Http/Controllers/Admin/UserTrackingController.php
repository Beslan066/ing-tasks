<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\Request;

class UserTrackingController extends Controller
{
    public function index()
    {
        $users = User::with(['sessions' => function($query) {
            $query->orderBy('last_activity', 'desc');
        }])->get();

        // Статистика
        $totalUsers = User::count();
        $newUsersCount = User::where('created_at', '>=', now()->subWeek())->count();

        // ИСПРАВЛЕНО: Активные сессии - те, у которых is_current = true
        $activeSessions = UserSession::where('is_current', true)->count();

        // Или активные за последние 5 минут
        $activeOnlineUsers = UserSession::where('last_activity', '>=', now()->subMinutes(5))
            ->distinct('user_id')
            ->count('user_id');

        $activePercentage = $totalUsers > 0 ? round(($activeSessions / $totalUsers) * 100) : 0;

        $uniqueDevices = UserSession::distinct('device_fingerprint')->count('device_fingerprint');
        $devicesCount = UserSession::distinct('device_type')->count('device_type');

        $countriesCount = UserSession::whereNotNull('country')
            ->distinct('country')
            ->count('country');

        return view('admin.users-tracking', compact(
            'users',
            'totalUsers',
            'newUsersCount',
            'activeSessions',
            'activePercentage',
            'uniqueDevices',
            'devicesCount',
            'countriesCount'
        ));
    }

    public function show(User $user)
    {
        $sessions = $user->sessions()->orderBy('last_activity', 'desc')->get();
        return view('admin.user-details', compact('user', 'sessions'));
    }

    public function map()
    {
        $sessions = UserSession::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('user')
            ->get();

        return view('admin.users-map', compact('sessions'));
    }

    public function deleteSession($sessionId)
    {
        $session = UserSession::findOrFail($sessionId);
        $session->delete();

        return response()->json(['success' => true]);
    }

    public function clearSessions($userId)
    {
        $user = User::findOrFail($userId);
        $user->sessions()->delete();

        return response()->json(['success' => true]);
    }
}
