<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    public function handle(Request $request, Closure $next, ...$requiredFeatures)
    {
        $user = Auth::user();

        if (!$user || !$user->company) {
            return $next($request);
        }

        $company = $user->company;

        // Проверка для премиум функций
        if (!empty($requiredFeatures)) {
            if ($company->license_type !== 'premium') {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'error' => 'Premium subscription required',
                        'required_features' => $requiredFeatures
                    ], 402);
                }

                return redirect()->route('licence.index')
                    ->with('warning', 'Эта функция доступна только на Премиум тарифе');
            }

            // Проверка активной подписки
            $subscription = $company->subscription;
            if (!$subscription || $subscription->isExpired()) {
                $company->downgradeToBasic();

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'Subscription expired'], 402);
                }

                return redirect()->route('licence.index')
                    ->with('error', 'Ваша премиум подписка истекла');
            }
        }

        // Проверка лимита пользователей
        if (!$company->canAddUser() && $request->routeIs('company.users.create')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'User limit reached'], 429);
            }

            return redirect()->route('licence.index')
                ->with('error', 'Достигнут лимит пользователей. Добавьте больше пользователей или обновите тариф.');
        }

        return $next($request);
    }
}
