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
    use DeviceInfoTrait; // Добавляем трейт для сбора информации

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
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

            // ОТЛАДКА: проверяем, доходит ли код до этого места
            \Log::info('User logged in: ' . $user->email);

            // Собираем информацию
            $deviceInfo = $this->getDeviceInfo($request);
            $geoInfo = $this->getGeolocation($request->ip());
            $fingerprint = $this->generateFingerprint($request);

            // ОТЛАДКА: смотрим, какие данные собираются
            \Log::info('Device info: ', $deviceInfo);
            \Log::info('Geo info: ', $geoInfo);
            \Log::info('IP: ' . $request->ip());

            // Создаем сессию
            try {
                $session = UserSession::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'last_activity' => now(),
                    'is_current' => true,
                    'device_fingerprint' => $fingerprint,
                    'user_agent' => $deviceInfo['user_agent'],
                    'device_type' => $deviceInfo['device_type'],
                    'browser' => $deviceInfo['browser'],
                    'os' => $deviceInfo['os'],
                    'country' => $geoInfo['country'],
                    'city' => $geoInfo['city'],
                    'latitude' => $geoInfo['latitude'],
                    'longitude' => $geoInfo['longitude'],
                    'address' => $geoInfo['address'],
                ]);

                \Log::info('Session created with ID: ' . $session->id);

            } catch (\Exception $e) {
                \Log::error('Error creating session: ' . $e->getMessage());
                dd($e->getMessage()); // Временная остановка для просмотра ошибки
            }

            $request->session()->put('user_session_id', $session->id);
        }

        return redirect()->intended(route('welcome', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            // Обновляем время активности при выходе
            $user->update(['last_activity_at' => now()]);

            // ========== ОБНОВЛЯЕМ СЕССИЮ ПРИ ВЫХОДЕ ==========
            // Получаем ID сессии из сессии Laravel
            $sessionId = $request->session()->get('user_session_id');

            if ($sessionId) {
                $userSession = UserSession::find($sessionId);
                if ($userSession) {
                    // Помечаем сессию как неактивную
                    $userSession->update([
                        'is_current' => false,
                        'last_activity' => now(),
                    ]);
                }
            }

            // Или обновляем все сессии этого пользователя, помечая их как неактивные
            UserSession::where('user_id', $user->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);
            // =================================================
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
