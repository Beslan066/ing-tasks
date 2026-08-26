<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Проверяем, есть ли у пользователя доступ к чату
        if (!$user->company_id) {
            return redirect()->route('dashboard')->with('error', 'Вы не состоите в компании');
        }

        $company = $user->company;

        // Проверяем премиум доступ
        $hasPremiumAccess = $company && $company->license_type === 'premium';
        $subscription = $company ? $company->subscription : null;
        $isSubscriptionActive = $subscription && !$subscription->isExpired();

        // Проверяем флаги из сессии для показа модального окна
        $showPremiumModal = session('show_chat_premium_modal', false);
        $showExpiredModal = session('show_chat_expired_modal', false);

        // Полный доступ к чату только если премиум и подписка активна
        $canAccessChat = $hasPremiumAccess && $isSubscriptionActive;

        return view('frontend.chat.index', [
            'user' => $user,
            'company' => $company,
            'isLeader' => $user->isLeader(),
            'isManager' => $user->isManagerRole(),
            'isCompanyOwner' => $user->isCompanyOwner(),
            'hasPremiumAccess' => $hasPremiumAccess,
            'isSubscriptionActive' => $isSubscriptionActive,
            'canAccessChat' => $canAccessChat,
            'showPremiumModal' => $showPremiumModal,
            'showExpiredModal' => $showExpiredModal,
        ]);
    }
}
