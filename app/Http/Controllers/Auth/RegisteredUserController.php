<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSession; // ВАЖНО: добавить этот импорт
use App\Traits\DeviceInfoTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    use DeviceInfoTrait;

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $avatarPath = null;

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = $avatar->storeAs('avatars', $avatarName, 'public');
            $this->createThumbnail($avatar, $avatarName);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => $avatarPath,
        ]);

        // Создаем сессию для нового пользователя
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

            \Log::info('User registered and session created', [
                'user_id' => $user->id,
                'session_id' => $session->id
            ]);

            // Сохраняем ID сессии
            session()->put('user_session_id', $session->id);

        } catch (\Exception $e) {
            \Log::error('Failed to create session during registration: ' . $e->getMessage());
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('welcome', absolute: false));
    }

    private function createThumbnail($avatar, $avatarName): void
    {
        $originalPath = $avatar->getRealPath();
        $thumbnailPath = storage_path('app/public/avatars/thumbnails/' . $avatarName);

        if (!file_exists(dirname($thumbnailPath))) {
            mkdir(dirname($thumbnailPath), 0755, true);
        }

        $imageInfo = getimagesize($originalPath);
        if (!$imageInfo) {
            return;
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mime = $imageInfo['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($originalPath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($originalPath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($originalPath);
                break;
            case 'image/webp':
                $sourceImage = imagecreatefromwebp($originalPath);
                break;
            default:
                return;
        }

        $thumbWidth = 100;
        $thumbHeight = 100;
        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);

        if ($mime == 'image/png' || $mime == 'image/gif' || $mime == 'image/webp') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefilledrectangle($thumbnail, 0, 0, $thumbWidth, $thumbHeight, $transparent);
        }

        imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0,
            $thumbWidth, $thumbHeight, $width, $height);

        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($thumbnail, $thumbnailPath, 90);
                break;
            case 'image/png':
                imagepng($thumbnail, $thumbnailPath, 9);
                break;
            case 'image/gif':
                imagegif($thumbnail, $thumbnailPath);
                break;
            case 'image/webp':
                imagewebp($thumbnail, $thumbnailPath, 90);
                break;
        }

        imagedestroy($sourceImage);
        imagedestroy($thumbnail);
    }
}
