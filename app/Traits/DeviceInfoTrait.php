<?php

namespace App\Traits;

use Illuminate\Http\Request;
use WhichBrowser\Parser;

trait DeviceInfoTrait
{
    protected function getDeviceInfo(Request $request): array
    {
        $userAgent = $request->userAgent();
        $result = new Parser($userAgent);

        // Определяем тип устройства
        $deviceType = 'desktop';
        if ($result->isType('mobile')) {
            $deviceType = 'mobile';
        } elseif ($result->isType('tablet')) {
            $deviceType = 'tablet';
        } elseif ($result->isType('bot')) {
            $deviceType = 'bot';
        }

        return [
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'browser' => $result->browser->getName() ?? 'Unknown',
            'os' => $result->os->toString() ?? 'Unknown',
            'device_fingerprint' => $this->generateFingerprint($request),
        ];
    }

    protected function generateFingerprint(Request $request): string
    {
        $data = [
            $request->userAgent(),
            $request->ip(),
            $request->header('accept-language'),
            $request->header('accept-encoding'),
        ];

        return hash('sha256', implode('|', $data));
    }

    protected function getGeolocation(string $ip): array
    {
        // Используем бесплатный API ip-api.com
        $url = "http://ip-api.com/json/{$ip}?fields=status,country,city,lat,lon,query";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if ($data && $data['status'] === 'success') {
                return [
                    'country' => $data['country'],
                    'city' => $data['city'],
                    'latitude' => $data['lat'],
                    'longitude' => $data['lon'],
                    'address' => "{$data['city']}, {$data['country']}",
                ];
            }
        }

        return [
            'country' => null,
            'city' => null,
            'latitude' => null,
            'longitude' => null,
            'address' => null,
        ];
    }
}
