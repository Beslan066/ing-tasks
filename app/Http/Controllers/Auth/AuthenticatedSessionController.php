<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\UserSession;
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

            // Отладка
            \Log::info('User logged in: ' . $user->email);

            // Собираем информацию
            $deviceInfo = $this->getDeviceInfo($request);
            $geoInfo = $this->getGeolocation($request->ip());
            $fingerprint = $this->generateFingerprint($request);

            // Отладка
            \Log::info('Device info: ', $deviceInfo);
            \Log::info('Geo info: ', $geoInfo);
            \Log::info('IP: ' . $request->ip());
            \Log::info('Fingerprint: ' . $fingerprint);

            // Деактивируем все предыдущие сессии этого пользователя
            UserSession::where('user_id', $user->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            // Создаем новую сессию
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

                \Log::info('Session created with ID: ' . $session->id);

                // Сохраняем ID сессии в сессию Laravel
                $request->session()->put('user_session_id', $session->id);

            } catch (\Exception $e) {
                \Log::error('Error creating session: ' . $e->getMessage());
                // Не прерываем выполнение, просто логируем ошибку
            }
        }

        return redirect()->intended(route('welcome', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            // Обновляем время активности при выходе
            $user->update(['last_activity_at' => now()]);

            // Получаем ID сессии из сессии Laravel
            $sessionId = $request->session()->get('user_session_id');

            if ($sessionId) {
                try {
                    $userSession = UserSession::find($sessionId);
                    if ($userSession) {
                        $userSession->update([
                            'is_current' => false,
                            'last_activity' => now(),
                        ]);
                        \Log::info('Session deactivated: ' . $sessionId);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error deactivating session: ' . $e->getMessage());
                }
            }

            // Страховка: деактивируем все активные сессии этого пользователя
            UserSession::where('user_id', $user->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
