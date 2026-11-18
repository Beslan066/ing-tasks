<?php
// app/Http/Controllers/Frontend/LicenceAndPaymentController.php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\AdditionalUserPurchase;
use App\Models\User;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\YooKassaApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LicenceAndPaymentController extends Controller
{
    protected PaymentService $paymentService;

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

            $company = $this->getUserCompany($authUser);

            if (!$company) {
                return redirect()->route('company.create')->with('error', 'Сначала создайте компанию');
            }

            $subscription = Subscription::where('company_id', $company->id)
                ->where('status', 'active')
                ->first();

            $currentPlan = $company->license_type ?? 'basic';

            $totalUsers = User::where('company_id', $company->id)->count();
            $usedUsers = $totalUsers;

            if ($currentPlan === 'premium') {
                if ($subscription) {
                    $maxUsers = $subscription->getTotalUserSlots();

                    Log::info('Premium user slots', [
                        'subscription_id' => $subscription->id,
                        'base_slots' => $subscription->base_user_slots,
                        'total_slots' => $maxUsers,
                    ]);
                } else {
                    $maxUsers = PaymentService::PREMIUM_BASE_USER_LIMIT;
                }
            } else {
                $maxUsers = PaymentService::BASIC_USER_LIMIT;
            }

            $storageStats = $company->getStorageStats();
            $usedStorageGB = round($storageStats['used'] / 1073741824, 2);
            $maxStorageGB = $currentPlan === 'premium' ? 1024 : 2;
            $premiumUntil = $subscription ? $subscription->expires_at->format('d.m.Y') : null;

            $features = $currentPlan === 'premium'
                ? $this->getPremiumFeaturesList()
                : $this->getBasicFeaturesList();

            $pendingPayment = Payment::where('company_id', $company->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

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
                'features',
                'pendingPayment'
            ));
        } catch (\Exception $e) {
            Log::error('Licence index error: ' . $e->getMessage());
            return back()->with('error', 'Произошла ошибка при загрузке страницы');
        }
    }

    protected function getUserCompany($user)
    {
        if ($user->company_id) {
            $company = $user->company;
            if ($company) {
                return $company;
            }
        }

        $company = \App\Models\Company::where('user_id', $user->id)->first();
        if ($company) {
            $user->update(['company_id' => $company->id]);
            return $company;
        }

        $company = \App\Models\Company::whereHas('users', function ($query) use ($user) {
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
        try {
            $request->validate([
                'period' => 'required|in:month,6months,year',
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Не авторизован'], 401);
            }

            $company = $this->getUserCompany($user);
            if (!$company) {
                return response()->json(['error' => 'Компания не найдена'], 404);
            }

            Log::info('Creating premium payment', [
                'company_id' => $company->id,
                'period' => $request->period,
            ]);

            $result = $this->paymentService->createPremiumPayment($company, $request->period);

            if ($result['success']) {
                session(['last_payment_id' => $result['payment']->provider_payment_id]);
                session(['last_payment_db_id' => $result['payment']->id]);

                return response()->json([
                    'success' => true,
                    'payment_url' => $result['payment_url'],
                    'amount' => $result['amount'],
                ]);
            }

            return response()->json(['error' => $result['error']], 500);
        } catch (\Exception $e) {
            Log::error('Premium payment error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createAdditionalUsersPayment(Request $request)
    {
        try {
            $request->validate([
                'user_count' => 'required|integer|min:1|max:100',
                'period' => 'required|in:month,6months,year',
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
                (int) $request->user_count,
                $request->period
            );

            if ($result['success']) {
                session(['last_payment_id' => $result['payment']->provider_payment_id]);
                session(['last_payment_db_id' => $result['payment']->id]);

                return response()->json([
                    'success' => true,
                    'payment_url' => $result['payment_url'],
                    'amount' => $result['amount'],
                ]);
            }

            return response()->json(['error' => $result['error']], 500);
        } catch (\Exception $e) {
            Log::error('Additional users payment error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function paymentCallback(Request $request)
    {
        Log::info('Payment callback received', [
            'full_url' => $request->fullUrl(),
            'session_payment_id' => session('last_payment_id'),
            'session_payment_db_id' => session('last_payment_db_id'),
            'all_params' => $request->all(),
        ]);

        $paymentDbId = session('last_payment_db_id');
        $paymentId = session('last_payment_id');

        session()->forget('last_payment_id');
        session()->forget('last_payment_db_id');

        if (!$paymentDbId && !$paymentId) {
            $user = Auth::user();
            if ($user && $user->company) {
                $payment = Payment::where('company_id', $user->company->id)
                    ->where('status', 'pending')
                    ->latest()
                    ->first();

                if ($payment) {
                    $paymentDbId = $payment->id;
                    $paymentId = $payment->provider_payment_id;
                }
            }
        }

        if (!$paymentDbId) {
            return redirect()->route('licence.index')->with('error', 'Платеж не найден. Пожалуйста, обратитесь в поддержку.');
        }

        $payment = Payment::find($paymentDbId);

        if (!$payment) {
            return redirect()->route('licence.index')->with('error', 'Платеж не найден в базе данных');
        }

        Log::info('Payment found, checking status', [
            'payment_id' => $payment->id,
            'provider_payment_id' => $payment->provider_payment_id,
            'current_status' => $payment->status,
        ]);

        $yooKassaApi = app(YooKassaApiService::class);
        $paymentInfo = $yooKassaApi->getPayment($payment->provider_payment_id);

        if ($paymentInfo && ($paymentInfo['status'] ?? null) === 'succeeded') {
            Log::info('Payment succeeded, activating subscription');
            $this->paymentService->handleSuccessfulPayment($payment, $paymentInfo);
            return redirect()->route('licence.index')->with('success', 'Подписка успешно активирована!');
        }

        Log::warning('Payment not succeeded yet', [
            'status' => $paymentInfo['status'] ?? 'unknown',
            'payment_id' => $payment->id,
        ]);

        return redirect()->route('licence.index')->with('warning', 'Платеж обрабатывается. Статус: ' . ($paymentInfo['status'] ?? 'ожидание'));
    }

    public function paymentWebhook(Request $request)
    {
        Log::info('Webhook received', ['payload' => $request->all()]);

        try {
            $result = $this->paymentService->handleWebhook($request->all());

            if (!$result['success']) {
                return response()->json(['error' => $result['error'] ?? 'Unknown error'], 400);
            }

            return response()->json(['status' => 'ok', 'message' => $result['message'] ?? 'Webhook received'], 200);
        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkPaymentStatus($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        $yooKassaApi = app(YooKassaApiService::class);
        $paymentInfo = $yooKassaApi->getPayment($payment->provider_payment_id);

        if ($paymentInfo && ($paymentInfo['status'] ?? null) === 'succeeded' && !$payment->isCompleted()) {
            $this->paymentService->handleSuccessfulPayment($payment, $paymentInfo);
            return redirect()->route('licence.index')->with('success', 'Платеж подтвержден!');
        }

        return redirect()->route('licence.index')->with('info', 'Статус платежа: ' . ($paymentInfo['status'] ?? 'неизвестен'));
    }

    public function manualActivate($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        if (!$payment->isCompleted()) {
            $this->paymentService->handleSuccessfulPayment($payment, ['manual_activate' => true]);
            return redirect()->route('licence.index')->with('success', 'Подписка активирована вручную!');
        }

        return redirect()->route('licence.index')->with('info', 'Подписка уже активна');
    }

    public function getPaymentInfo(Request $request)
    {
        $period = $request->get('period', 'month');
        $userCount = (int) $request->get('user_count', 1);

        $premiumPrice = $this->paymentService->calculatePremiumPrice($period);
        $userPrice = $this->paymentService->calculateUserPrice($userCount, $period);

        return response()->json([
            'premium' => $premiumPrice,
            'users' => $userPrice,
        ]);
    }

    protected function getPremiumFeaturesList(): array
    {
        return [
            'До 15 пользователей (можно расширять)',
            'Файловое хранилище до 1 ТБ',
            'Приоритетная поддержка 24/7',
            'Управление задачами и проектами',
            'Мессенджер',
            'Расширенная аналитика и отчеты',
            'СМС-оповещение о задачах',
        ];
    }

    protected function getBasicFeaturesList(): array
    {
        return [
            'До 5 пользователей',
            'Файловое хранилище до 2 ГБ',
            'Управление задачами',
            'Мессенджер',
            'Команды и проекты',
        ];
    }
}