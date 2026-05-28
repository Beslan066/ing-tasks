<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
            // Убираем device_fingerprint отсюда, он будет отдельно
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
        // Пропускаем локальные IP
        if ($this->isLocalIp($ip)) {
            return ['country' => null, 'city' => null, 'latitude' => null, 'longitude' => null, 'address' => null];
        }

        $cacheKey = 'geo_ip_' . $ip;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $services = [
            'ipapi' => ['url' => "https://ipapi.co/{$ip}/json/", 'parse' => function($d) {
                return ['country' => $d['country_name'] ?? null, 'city' => $d['city'] ?? null, 'latitude' => $d['latitude'] ?? null, 'longitude' => $d['longitude'] ?? null, 'address' => isset($d['city']) ? "{$d['city']}, {$d['country_name']}" : null];
            }],
            'ipwhois' => ['url' => "https://ipwho.is/{$ip}", 'parse' => function($d) {
                return ['country' => $d['country'] ?? null, 'city' => $d['city'] ?? null, 'latitude' => $d['latitude'] ?? null, 'longitude' => $d['longitude'] ?? null, 'address' => isset($d['city']) ? "{$d['city']}, {$d['country']}" : null];
            }],
            'ip-api' => ['url' => "http://ip-api.com/json/{$ip}", 'parse' => function($d) {
                if ($d['status'] === 'success') return ['country' => $d['country'] ?? null, 'city' => $d['city'] ?? null, 'latitude' => $d['lat'] ?? null, 'longitude' => $d['lon'] ?? null, 'address' => isset($d['city']) ? "{$d['city']}, {$d['country']}" : null];
                return null;
            }],
        ];

        foreach ($services as $service) {
            try {
                $response = Http::timeout(3)->get($service['url']);
                if ($response->successful()) {
                    $data = $response->json();
                    $result = $service['parse']($data);
                    if ($result && ($result['country'] || $result['city'])) {
                        Cache::put($cacheKey, $result, now()->addDay());
                        return $result;
                    }
                }
            } catch (\Exception $e) {
                \Log::warning("Geo service {$service['url']} failed: " . $e->getMessage());
            }
        }

        return ['country' => null, 'city' => null, 'latitude' => null, 'longitude' => null, 'address' => null];
    }

    /**
     * Проверка на локальный IP
     */
    protected function isLocalIp($ip)
    {
        $localIps = [
            '127.0.0.1',
            '::1',
            'localhost',
        ];

        if (in_array($ip, $localIps)) {
            return true;
        }

        // Проверка на локальные диапазоны
        $privateRanges = [
            '192.168.',
            '10.',
            '172.16.', '172.17.', '172.18.', '172.19.',
            '172.20.', '172.21.', '172.22.', '172.23.',
            '172.24.', '172.25.', '172.26.', '172.27.',
            '172.28.', '172.29.', '172.30.', '172.31.'
        ];

        foreach ($privateRanges as $range) {
            if (strpos($ip, $range) === 0) {
                return true;
            }
        }

        return false;
    }
}
