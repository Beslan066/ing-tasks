<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Intervention\Image\Image;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
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

            // Создаем уникальное имя для файла
            $avatarName = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();

            // Сохраняем оригинальный файл
            $avatarPath = $avatar->storeAs('avatars', $avatarName, 'public');

            // Создаем миниатюру используя базовый PHP
            $this->createThumbnail($avatar, $avatarName);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => $avatarPath,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Создание миниатюры с помощью GD
     */
    private function createThumbnail($avatar, $avatarName): void
    {
        $originalPath = $avatar->getRealPath();
        $thumbnailPath = storage_path('app/public/avatars/thumbnails/' . $avatarName);

        // Создаем директорию если не существует
        if (!file_exists(dirname($thumbnailPath))) {
            mkdir(dirname($thumbnailPath), 0755, true);
        }

        // Получаем информацию об изображении
        $imageInfo = getimagesize($originalPath);
        if (!$imageInfo) {
            return;
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mime = $imageInfo['mime'];

        // Создаем изображение из файла в зависимости от типа
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

        // Размеры миниатюры
        $thumbWidth = 100;
        $thumbHeight = 100;

        // Создаем новое изображение для миниатюры
        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);

        // Для PNG и GIF сохраняем прозрачность
        if ($mime == 'image/png' || $mime == 'image/gif' || $mime == 'image/webp') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefilledrectangle($thumbnail, 0, 0, $thumbWidth, $thumbHeight, $transparent);
        }

        // Копируем и изменяем размер
        imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0,
            $thumbWidth, $thumbHeight, $width, $height);

        // Сохраняем миниатюру
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

        // Освобождаем память
        imagedestroy($sourceImage);
        imagedestroy($thumbnail);
    }
}
