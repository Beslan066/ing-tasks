<?php
// app/Http/Controllers/Frontend/LicenceAndPaymentController.php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\AdditionalUserPurchase;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicenceAndPaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        try {
            $authUser = auth()->user();

            if (!$authUser) {
                return redirect()->route('login');
            }

            // ВАЖНО: Определяем компанию пользователя
            // Если у пользователя company_id = null, нужно найти компанию через другие связи
            $company = $this->getUserCompany($authUser);

            if (!$company) {
                return redirect()->route('company.create')->with('error', 'Сначала создайте компанию');
            }

            // Получаем активную подписку
            $subscription = Subscription::where('company_id', $company->id)
                ->where('status', 'active')
                ->first();

            // Текущий план компании
            $currentPlan = $company->license_type ?? 'basic';

            // ПОДСЧЕТ ПОЛЬЗОВАТЕЛЕЙ - считаем ВСЕХ пользователей компании
            $totalUsers = User::where('company_id', $company->id)->count();
            $activeUsers = User::where('company_id', $company->id)->where('is_active', true)->count();

            // Для отображения используем ВСЕХ пользователей компании
            $usedUsers = $totalUsers;

            // Лимит пользователей
            if ($currentPlan === 'premium') {
                if ($subscription) {
                    $baseSlots = $subscription->base_user_slots ?? 15;
                    $additionalSlots = AdditionalUserPurchase::where('company_id', $company->id)
                        ->where('is_active', true)
                        ->where('expires_at', '>', now())
                        ->sum('user_count');
                    $maxUsers = $baseSlots + $additionalSlots;
                } else {
                    $maxUsers = 15;
                }
            } else {
                $maxUsers = 5; // Базовый тариф - 5 пользователей
            }

            // Данные о хранилище
            $storageStats = $company->getStorageStats();

            // Форматируем использованное хранилище
            $usedStorageGB = round($storageStats['used'] / 1073741824, 2);
            $maxStorageGB = $currentPlan === 'premium' ? 1024 : 2;

            // Получаем дату окончания подписки
            $premiumUntil = $subscription ? $subscription->expires_at->format('d.m.Y') : null;

            // Получаем список доступных функций
            $features = $currentPlan === 'premium'
                ? $this->getPremiumFeaturesList()
                : $this->getBasicFeaturesList();

            // Для отладки - можно временно вывести в лог
            \Log::info('Licence page data', [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'current_plan' => $currentPlan,
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'used_users_display' => $usedUsers,
                'max_users' => $maxUsers,
                'used_storage_gb' => $usedStorageGB,
                'max_storage_gb' => $maxStorageGB
            ]);

            return view('frontend.licence_and_payments.index', compact(
                'company',
                'subscription',
                'currentPlan',
                'usedUsers',
                'maxUsers',
                'usedStorageGB',
                'maxStorageGB',
                'storageStats',
                'premiumUntil',
                'features'
            ));

        } catch (\Exception $e) {
            \Log::error('Licence index error: ' . $e->getMessage());
            return back()->with('error', 'Произошла ошибка при загрузке страницы');
        }
    }

    /**
     * Получение компании пользователя
     * Учитывает случай, когда у пользователя company_id = null
     */
    protected function getUserCompany($user)
    {
        // Сначала пробуем получить через прямую связь
        if ($user->company_id) {
            $company = $user->company;
            if ($company) {
                return $company;
            }
        }

        // Если company_id = null, ищем компанию, где пользователь является владельцем
        $company = \App\Models\Company::where('user_id', $user->id)->first();
        if ($company) {
            // Обновляем пользователя, чтобы в следующий раз было правильно
            $user->update(['company_id' => $company->id]);
            return $company;
        }

        // Если не нашли, ищем компанию, где есть этот пользователь
        $company = \App\Models\Company::whereHas('users', function($query) use ($user) {
            $query->where('users.id', $user->id);
        })->first();

        if ($company) {
            $user->update(['company_id' => $company->id]);
            return $company;
        }

        return null;
    }

    public function createPremiumPayment(Request $request)
    {
        $request->validate([
            'period' => 'required|in:month,6months,year'
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Не авторизован'], 401);
        }

        $company = $this->getUserCompany($user);

        if (!$company) {
            return response()->json(['error' => 'Компания не найдена'], 404);
        }

        $result = $this->paymentService->createPremiumPayment($company, $request->period);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'payment_url' => $result['payment_url'],
                'amount' => $result['amount']
            ]);
        }

        return response()->json(['error' => $result['error']], 500);
    }

    public function createAdditionalUsersPayment(Request $request)
    {
        $request->validate([
            'user_count' => 'required|integer|min:1|max:100',
            'period' => 'required|in:month,6months,year'
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Не авторизован'], 401);
        }

        $company = $this->getUserCompany($user);

        if (!$company) {
            return response()->json(['error' => 'Компания не найдена'], 404);
        }

        if ($company->license_type !== 'premium') {
            return response()->json(['error' => 'Дополнительные пользователи доступны только на Премиум тарифе'], 400);
        }

        $result = $this->paymentService->createAdditionalUsersPayment(
            $company,
            $request->user_count,
            $request->period
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'payment_url' => $result['payment_url'],
                'amount' => $result['amount']
            ]);
        }

        return response()->json(['error' => $result['error']], 500);
    }

    public function paymentCallback(Request $request)
    {
        return redirect()->route('licence.index')->with('success', 'Платеж успешно выполнен!');
    }

    public function getPaymentInfo(Request $request)
    {
        $period = $request->get('period', 'month');
        $userCount = $request->get('user_count', 1);

        $premiumPrice = $this->paymentService->calculatePremiumPrice($period);
        $userPrice = $this->paymentService->calculateUserPrice($userCount, $period);

        return response()->json([
            'premium' => $premiumPrice,
            'users' => $userPrice
        ]);
    }

    public function paymentWebhook(Request $request)
    {
        $result = $this->paymentService->handleWebhook($request->all());

        if ($result['success']) {
            return response()->json(['status' => 'ok'], 200);
        }

        return response()->json(['error' => $result['error']], 400);
    }


    protected function getPremiumFeaturesList(): array
    {
        return [
            'До 15 пользователей (можно расширять)',
            'Файловое хранилище до 1 ТБ',
            'Приоритетная поддержка 24/7',
            'Управление задачами и проектами',
            'Мессенджер',
        ];
    }

    protected function getBasicFeaturesList(): array
    {
        return [
            'До 5 пользователей',
            'Файловое хранилище до 2 ГБ',
            'Управление задачами',
            'Мессенджер',
            'Команды и проекты'
        ];
    }
}
