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
            return [
                'country' => 'Локальный',
                'city' => 'Локальная сеть',
                'latitude' => null,
                'longitude' => null,
                'address' => 'Локальный IP',
            ];
        }

        // Пробуем получить из кэша
        $cacheKey = 'geo_ip_' . $ip;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Список API для получения геолокации
        $apis = [
            [
                'url' => "http://ip-api.com/json/{$ip}?fields=status,country,city,lat,lon,query",
                'parse' => function($data) {
                    if ($data && isset($data['status']) && $data['status'] === 'success') {
                        return [
                            'country' => $data['country'] ?? null,
                            'city' => $data['city'] ?? null,
                            'latitude' => $data['lat'] ?? null,
                            'longitude' => $data['lon'] ?? null,
                            'address' => isset($data['city']) ? "{$data['city']}, {$data['country']}" : null,
                        ];
                    }
                    return null;
                }
            ],
            [
                'url' => "https://freeipapi.com/api/json/{$ip}",
                'parse' => function($data) {
                    if ($data && !isset($data['error'])) {
                        return [
                            'country' => $data['countryName'] ?? null,
                            'city' => $data['cityName'] ?? null,
                            'latitude' => $data['latitude'] ?? null,
                            'longitude' => $data['longitude'] ?? null,
                            'address' => isset($data['cityName']) ? "{$data['cityName']}, {$data['countryName']}" : null,
                        ];
                    }
                    return null;
                }
            ]
        ];

        foreach ($apis as $api) {
            try {
                $response = Http::timeout(5)->get($api['url']);

                if ($response->successful()) {
                    $data = $response->json();
                    $result = $api['parse']($data);

                    if ($result && ($result['latitude'] || $result['country'])) {
                        Cache::put($cacheKey, $result, now()->addDay());
                        return $result;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Geolocation API failed: " . $e->getMessage());
                continue;
            }
        }

        // Если ничего не сработало, возвращаем null значения
        return [
            'country' => null,
            'city' => null,
            'latitude' => null,
            'longitude' => null,
            'address' => null,
        ];
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
