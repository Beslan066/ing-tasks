<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait GeolocationTrait
{
    /**
     * Получение геолокации по IP адресу
     */
    protected function getGeolocationByIp($ip)
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

        // Список API для получения геолокации (по очереди)
        $apis = [
            // ip-api.com (бесплатно, до 45 запросов в минуту)
            [
                'url' => "http://ip-api.com/json/{$ip}?fields=status,country,city,lat,lon,query",
                'parse' => function($data) {
                    if ($data && $data['status'] === 'success') {
                        return [
                            'country' => $data['country'],
                            'city' => $data['city'],
                            'latitude' => $data['lat'],
                            'longitude' => $data['lon'],
                            'address' => "{$data['city']}, {$data['country']}",
                        ];
                    }
                    return null;
                }
            ],
            // ipwhois.io (бесплатно, до 1000 запросов в день)
            [
                'url' => "http://ipwhois.io/json/{$ip}",
                'parse' => function($data) {
                    if ($data && !isset($data['error'])) {
                        return [
                            'country' => $data['country'] ?? null,
                            'city' => $data['city'] ?? null,
                            'latitude' => $data['latitude'] ?? null,
                            'longitude' => $data['longitude'] ?? null,
                            'address' => isset($data['city']) ? "{$data['city']}, {$data['country']}" : null,
                        ];
                    }
                    return null;
                }
            ],
            // freeipapi.com (бесплатно, без ограничений)
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
            ],
            // ipapi.co (бесплатно, 1000 запросов в день)
            [
                'url' => "https://ipapi.co/{$ip}/json/",
                'parse' => function($data) {
                    if ($data && !isset($data['error'])) {
                        return [
                            'country' => $data['country_name'] ?? null,
                            'city' => $data['city'] ?? null,
                            'latitude' => $data['latitude'] ?? null,
                            'longitude' => $data['longitude'] ?? null,
                            'address' => isset($data['city']) ? "{$data['city']}, {$data['country_name']}" : null,
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

                    if ($result && $result['latitude'] && $result['longitude']) {
                        // Сохраняем в кэш на 24 часа
                        Cache::put($cacheKey, $result, now()->addDay());
                        return $result;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Geolocation API failed: " . $e->getMessage());
                continue;
            }
        }

        // Если ничего не сработало, возвращаем заглушку с координатами Ингушетии
        return [
            'country' => 'Россия',
            'city' => 'Ингушетия',
            'latitude' => 43.1667,  // Широта Ингушетии
            'longitude' => 44.8333, // Долгота Ингушетии
            'address' => 'Республика Ингушетия, Россия',
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
        if (strpos($ip, '192.168.') === 0 ||
            strpos($ip, '10.') === 0 ||
            strpos($ip, '172.16.') === 0 ||
            strpos($ip, '172.17.') === 0 ||
            strpos($ip, '172.18.') === 0 ||
            strpos($ip, '172.19.') === 0 ||
            strpos($ip, '172.20.') === 0 ||
            strpos($ip, '172.21.') === 0 ||
            strpos($ip, '172.22.') === 0 ||
            strpos($ip, '172.23.') === 0 ||
            strpos($ip, '172.24.') === 0 ||
            strpos($ip, '172.25.') === 0 ||
            strpos($ip, '172.26.') === 0 ||
            strpos($ip, '172.27.') === 0 ||
            strpos($ip, '172.28.') === 0 ||
            strpos($ip, '172.29.') === 0 ||
            strpos($ip, '172.30.') === 0 ||
            strpos($ip, '172.31.') === 0) {
            return true;
        }

        return false;
    }
}
