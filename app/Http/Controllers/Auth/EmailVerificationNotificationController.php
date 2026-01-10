<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Если пользователь зашел через OAuth и email подтвержден соцсетью
        if ($this->shouldSkipVerification($user)) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    /**
     * Проверяем, нужно ли пропустить верификацию email
     */
    private function shouldSkipVerification($user): bool
    {
        // Пользователь зашел через соцсеть и email подтвержден
        if ($user->provider && $user->email_verified_at) {
            return true;
        }

        // Для OAuth пользователей с временным email
        if ($user->provider && str_contains($user->email, '.temp')) {
            $user->forceFill([
                'email_verified_at' => now(),
            ])->save();
            return true;
        }

        return false;
    }
}
