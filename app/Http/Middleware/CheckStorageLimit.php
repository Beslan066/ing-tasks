<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\StorageService;

class CheckStorageLimit
{
    protected $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !$user->company) {
            return $next($request);
        }

        // Проверяем только для маршрутов загрузки файлов
        if ($request->is('files/upload') || $request->routeIs('files.upload')) {
            if (!$this->storageService->checkUserUploadPermission($user->role->name)) {
                return redirect()->back()->with('error', 'У вас нет прав для загрузки файлов');
            }
        }

        return $next($request);
    }
}
