<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use App\Traits\DeviceInfoTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackUserSession
{
    use DeviceInfoTrait;

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $ip = $request->ip();
            $fingerprint = $this->generateFingerprint($request);

            // Поиск существующей сессии для этого устройства и IP
            $session = UserSession::where('user_id', $user->id)
                ->where('ip_address', $ip)
                ->where('device_fingerprint', $fingerprint)
                ->first();

            if (!$session) {
                // Собираем информацию об устройстве
                $deviceInfo = $this->getDeviceInfo($request);
                $geoInfo = $this->getGeolocation($ip);

                // Создаем новую сессию
                $session = UserSession::create(array_merge([
                    'user_id' => $user->id,
                    'ip_address' => $ip,
                    'last_activity' => now(),
                    'is_current' => true,
                    'device_fingerprint' => $fingerprint,
                ], $deviceInfo, $geoInfo));
            } else {
                // Обновляем существующую сессию
                $session->update([
                    'last_activity' => now(),
                    'is_current' => true,
                ]);
            }

            // ВАЖНО: Снимаем флаг is_current с других сессий этого пользователя
            UserSession::where('user_id', $user->id)
                ->where('id', '!=', $session->id)
                ->update(['is_current' => false]);
        }

        return $next($request);
    }
}
