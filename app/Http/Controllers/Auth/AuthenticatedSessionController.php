<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\UserSession;
use App\Models\UserOnlineSession; // ДОБАВИТЬ
use App\Traits\DeviceInfoTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    use DeviceInfoTrait;

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user) {
            $user->update([
                'last_activity_at' => now(),
                'is_active' => true
            ]);

            // ===== СОЗДАЕМ ПОДРОБНУЮ СЕССИЮ (UserSession) =====
            $deviceInfo = $this->getDeviceInfo($request);
            $geoInfo = $this->getGeolocation($request->ip());
            $fingerprint = $this->generateFingerprint($request);

            try {
                $session = UserSession::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $deviceInfo['user_agent'],
                    'device_type' => $deviceInfo['device_type'],
                    'browser' => $deviceInfo['browser'],
                    'os' => $deviceInfo['os'],
                    'country' => $geoInfo['country'],
                    'city' => $geoInfo['city'],
                    'latitude' => $geoInfo['latitude'],
                    'longitude' => $geoInfo['longitude'],
                    'address' => $geoInfo['address'],
                    'device_fingerprint' => $fingerprint,
                    'last_activity' => now(),
                    'is_current' => true,
                ]);

                $request->session()->put('user_session_id', $session->id);

            } catch (\Exception $e) {
                \Log::error('Error creating UserSession: ' . $e->getMessage());
            }

            // ===== СОЗДАЕМ ОНЛАЙН СЕССИЮ (UserOnlineSession) =====
            try {
                UserOnlineSession::create([
                    'user_id' => $user->id,
                    'login_at' => now(),
                    'session_id' => $request->session()->getId(),
                    'date' => now()->toDateString(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'last_activity_at' => now(),
                ]);

                \Log::info('UserOnlineSession created for user: ' . $user->id);

            } catch (\Exception $e) {
                \Log::error('Error creating UserOnlineSession: ' . $e->getMessage());
            }
        }

        return redirect()->intended(route('welcome', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            $user->update(['last_activity_at' => now()]);

            // ===== ЗАКРЫВАЕМ ПОДРОБНУЮ СЕССИЮ (UserSession) =====
            $sessionId = $request->session()->get('user_session_id');

            if ($sessionId) {
                try {
                    $userSession = UserSession::find($sessionId);
                    if ($userSession) {
                        $userSession->update([
                            'is_current' => false,
                            'last_activity' => now(),
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error closing UserSession: ' . $e->getMessage());
                }
            }

            // Деактивируем все UserSession
            UserSession::where('user_id', $user->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            // ===== ЗАКРЫВАЕМ ОНЛАЙН СЕССИЮ (UserOnlineSession) =====
            try {
                // Находим активную онлайн сессию
                $onlineSession = UserOnlineSession::where('user_id', $user->id)
                    ->whereNull('logout_at')
                    ->first();

                if ($onlineSession) {
                    $onlineSession->endSession(); // Теперь этот метод существует!
                    \Log::info('UserOnlineSession closed for user: ' . $user->id);
                }
            } catch (\Exception $e) {
                \Log::error('Error closing UserOnlineSession: ' . $e->getMessage());
            }
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
