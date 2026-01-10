<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OAuthController extends Controller
{
    /**
     * Редирект на VK OAuth
     */
    public function redirectToVK()
    {
        $url = 'https://oauth.vk.com/authorize?' . http_build_query([
                'client_id' => config('services.vk.client_id'),
                'redirect_uri' => config('services.vk.redirect'),
                'response_type' => 'code',
                'scope' => 'email',
                'state' => Str::random(40),
                'display' => 'page',
                'v' => '5.199',
            ]);

        return redirect()->away($url);
    }

    /**
     * Редирект на Яндекс OAuth
     */
    public function redirectToYandex()
    {
        // Используем rawurlencode для корректного кодирования кириллического домена
        $redirectUri = rawurlencode(config('services.yandex.redirect'));

        $url = 'https://oauth.yandex.ru/authorize?' . http_build_query([
                'client_id' => config('services.yandex.client_id'),
                'response_type' => 'code',
                'redirect_uri' => config('services.yandex.redirect'), // Уже должно быть закодировано в конфиге
                'state' => Str::random(40),
            ]);

        // Альтернативный вариант - формируем URL вручную
        $clientId = config('services.yandex.client_id');
        $redirectUri = config('services.yandex.redirect');

        $url = "https://oauth.yandex.ru/authorize?client_id={$clientId}&response_type=code&redirect_uri=" . urlencode($redirectUri);

        return redirect()->away($url);
    }

    /**
     * Обработка callback от VK
     */
    public function handleVKCallback(Request $request)
    {
        try {
            $code = $request->get('code');

            if (!$code) {
                throw new \Exception('Код авторизации не получен');
            }

            // 1. Получаем access token
            $tokenResponse = Http::asForm()->post('https://oauth.vk.com/access_token', [
                'client_id' => config('services.vk.client_id'),
                'client_secret' => config('services.vk.client_secret'),
                'redirect_uri' => config('services.vk.redirect'),
                'code' => $code,
            ]);

            if (!$tokenResponse->successful()) {
                throw new \Exception('Ошибка получения токена VK: ' . $tokenResponse->body());
            }

            $tokenData = $tokenResponse->json();

            // 2. Получаем информацию о пользователе
            $userResponse = Http::get('https://api.vk.com/method/users.get', [
                'access_token' => $tokenData['access_token'],
                'v' => '5.199',
                'fields' => 'photo_200,has_photo,photo_max_orig,email',
            ]);

            if (!$userResponse->successful()) {
                throw new \Exception('Ошибка получения данных пользователя VK: ' . $userResponse->body());
            }

            $userData = $userResponse->json();

            if (!isset($userData['response'][0])) {
                throw new \Exception('Данные пользователя не получены');
            }

            $vkUser = $userData['response'][0];

            // 3. Создаем или находим пользователя
            $user = $this->findOrCreateUser([
                'provider' => 'vkontakte',
                'provider_id' => (string) $vkUser['id'],
                'name' => trim($vkUser['first_name'] . ' ' . $vkUser['last_name']),
                'email' => $tokenData['email'] ?? $vkUser['id'] . '@vk.temp',
                'avatar' => $vkUser['photo_200'] ?? ($vkUser['photo_max_orig'] ?? null),
                'email_verified' => isset($tokenData['email']),
            ]);

            Auth::login($user, true);

            // Обновляем время последнего входа
            $user->update(['last_login_at' => now()]);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('VK OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Ошибка авторизации через VK: ' . $e->getMessage());
        }
    }

    /**
     * Обработка callback от Яндекс
     */
    public function handleYandexCallback(Request $request)
    {
        try {
            $code = $request->get('code');

            if (!$code) {
                throw new \Exception('Код авторизации не получен');
            }

            // 1. Получаем access token
            $tokenResponse = Http::asForm()->post('https://oauth.yandex.ru/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => config('services.yandex.client_id'),
                'client_secret' => config('services.yandex.client_secret'),
                'redirect_uri' => config('services.yandex.redirect'),
            ]);

            if (!$tokenResponse->successful()) {
                throw new \Exception('Ошибка получения токена Яндекс: ' . $tokenResponse->body());
            }

            $tokenData = $tokenResponse->json();

            // 2. Получаем информацию о пользователе
            $userResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $tokenData['access_token'],
            ])->get('https://login.yandex.ru/info', [
                'format' => 'json',
            ]);

            if (!$userResponse->successful()) {
                throw new \Exception('Ошибка получения данных пользователя Яндекс: ' . $userResponse->body());
            }

            $yandexUser = $userResponse->json();

            // 3. Создаем или находим пользователя
            $user = $this->findOrCreateUser([
                'provider' => 'yandex',
                'provider_id' => (string) $yandexUser['id'],
                'name' => trim(($yandexUser['first_name'] ?? '') . ' ' . ($yandexUser['last_name'] ?? '')),
                'email' => $yandexUser['default_email'] ?? $yandexUser['id'] . '@yandex.temp',
                'avatar' => $this->getYandexAvatar($yandexUser),
                'email_verified' => true,
            ]);

            Auth::login($user, true);

            // Обновляем время последнего входа
            $user->update(['last_login_at' => now()]);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('Yandex OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Ошибка авторизации через Яндекс: ' . $e->getMessage());
        }
    }

    /**
     * Получаем аватар Яндекс пользователя
     */
    private function getYandexAvatar(array $userData): ?string
    {
        if (isset($userData['default_avatar_id']) && !empty($userData['default_avatar_id'])) {
            return 'https://avatars.yandex.net/get-yapic/' . $userData['default_avatar_id'] . '/islands-200';
        }

        return null;
    }

    /**
     * Находим или создаем пользователя
     */
    private function findOrCreateUser(array $data): User
    {
        // Сначала ищем по provider_id
        $user = User::where('provider', $data['provider'])
            ->where('provider_id', $data['provider_id'])
            ->first();

        if (!$user) {
            // Создаем нового пользователя
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(24)),
                'provider' => $data['provider'],
                'provider_id' => $data['provider_id'],
                'provider_avatar' => $data['avatar'],
                'is_active' => true,
                // Автоматически подтверждаем email для OAuth
                'email_verified_at' => $data['email_verified'] ? now() : null,
            ]);
        } else {
            // Обновляем существующего пользователя
            $user->update([
                'provider' => $data['provider'],
                'provider_id' => $data['provider_id'],
                'provider_avatar' => $data['avatar'],
                // Если email_verified_at не установлен, устанавливаем его
                'email_verified_at' => $user->email_verified_at ?? ($data['email_verified'] ? now() : null),
            ]);
        }

        return $user;
    }
}
