<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckChatAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !$user->company) {
            return $next($request);
        }

        $company = $user->company;

        // Проверяем, что подписка премиум и активна
        if ($company->license_type !== 'premium') {
            // Для AJAX запросов
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Premium subscription required',
                    'message' => 'Мессенджер доступен только на Премиум тарифе',
                    'code' => 'premium_required'
                ], 402);
            }

            // Если это сам маршрут чата - показываем страницу с модалкой без редиректа
            if ($request->route()->getName() === 'chat.index') {
                // Просто передаем запрос дальше, а в контроллере покажем модалку
                session()->flash('show_chat_premium_modal', true);
                return $next($request);
            }

            // Для других маршрутов чата - редирект на index с флагом
            session()->flash('show_chat_premium_modal', true);
            return redirect()->route('chat.index');
        }

        // Проверка активной подписки
        $subscription = $company->subscription;
        if (!$subscription || $subscription->isExpired()) {
            $company->downgradeToBasic();

            // Для AJAX запросов
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Subscription expired',
                    'message' => 'Ваша премиум подписка истекла',
                    'code' => 'subscription_expired'
                ], 402);
            }

            // Если это сам маршрут чата - показываем страницу с модалкой без редиректа
            if ($request->route()->getName() === 'chat.index') {
                session()->flash('show_chat_expired_modal', true);
                return $next($request);
            }

            // Для других маршрутов чата - редирект на index с флагом
            session()->flash('show_chat_expired_modal', true);
            return redirect()->route('chat.index');
        }

        return $next($request);
    }
}
