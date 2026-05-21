<?php

namespace App\Http\Controllers;

use App\Models\UserOnlineSession;
use Illuminate\Http\Request;

class UserLocationController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = auth()->user();

        // Обновляем текущую сессию
        $session = UserOnlineSession::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest()
            ->first();

        if ($session) {
            $session->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'city' => 'Определено через GPS',
                'country' => 'GPS координаты',
            ]);
        }

        return response()->json(['success' => true]);
    }
}
