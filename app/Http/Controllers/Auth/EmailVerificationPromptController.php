<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        // Если пользователь зашел через OAuth И email был получен от соцсети
        // Или email уже подтвержден
        if ($this->shouldSkipVerification($user)) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        return $user->hasVerifiedEmail()
            ? redirect()->intended(route('dashboard', absolute: false))
            : view('auth.verify-email');
    }

    /**
     * Проверяем, нужно ли пропустить верификацию email
     */
    private function shouldSkipVerification($user): bool
    {
        // 1. Email уже подтвержден
        if ($user->hasVerifiedEmail()) {
            return true;
        }

        // 2. Пользователь зашел через соцсеть и email от соцсети
        if ($user->provider && $user->email_verified_at) {
            return true;
        }

        // 3. Для OAuth пользователей с временным email (содержит .temp)
        if ($user->provider && str_contains($user->email, '.temp')) {
            // Автоматически отмечаем как подтвержденный, если это временный email
            $user->forceFill([
                'email_verified_at' => now(),
            ])->save();
            return true;
        }

        return false;
    }
}
