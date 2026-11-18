<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\AdditionalUserPurchase;
use App\Models\StorageUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    protected YooKassaApiService $yooKassaApi;
    protected bool $testMode;

    // Лимиты
    public const BASIC_USER_LIMIT = 5;
    public const PREMIUM_BASE_USER_LIMIT = 15;

    // Хранилище (в байтах)
    public const BASIC_STORAGE_LIMIT = 2 * 1073741824;      // 2 ГБ
    public const PREMIUM_STORAGE_LIMIT = 1073741824000;      // ~1 ТБ (как в исходнике)

    public function __construct(YooKassaApiService $yooKassaApi)
    {
        $this->yooKassaApi = $yooKassaApi;
        $this->testMode = (bool) config('payment.test_mode', true);
    }

    /* =====================================================================
     |  Создание платежей
     ===================================================================== */

    public function createPremiumPayment(Company $company, string $period, array $metadata = []): array
    {
        $priceData = $this->calculatePremiumPrice($period);

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'company_id' => $company->id,
                'payment_type' => 'subscription',
                'status' => 'pending',
                'amount' => $priceData['total'],
                'currency' => 'RUB',
                'period' => $period,
                'payment_provider' => 'yookassa',
                'provider_payment_id' => $this->generatePaymentId(),
                'metadata' => array_merge($metadata, [
                    'type' => 'premium_subscription',
                    'original_price' => $priceData['original'],
                    'discount' => $priceData['discount'],
                    'discount_percent' => $priceData['discount_percent'],
                ]),
            ]);
            DB::commit();

            $paymentData = $this->preparePaymentData($payment, $company, $priceData);
            $yooKassaResponse = $this->yooKassaApi->createPayment($paymentData);

            if (!$yooKassaResponse || !isset($yooKassaResponse['id'])) {
                $payment->update(['status' => 'failed']);
                return ['success' => false, 'error' => 'Ошибка создания платежа в YooKassa'];
            }

            $payment->update([
                'provider_payment_id' => $yooKassaResponse['id'],
                'provider_payment_url' => $yooKassaResponse['confirmation']['confirmation_url'] ?? null,
                'provider_data' => $yooKassaResponse,
            ]);

            return [
                'success' => true,
                'payment' => $payment,
                'payment_url' => $yooKassaResponse['confirmation']['confirmation_url'] ?? null,
                'amount' => $priceData['total'],
            ];
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Premium payment creation failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function createAdditionalUsersPayment(Company $company, int $userCount, string $period, array $metadata = []): array
    {
        if ($company->license_type !== 'premium') {
            return ['success' => false, 'error' => 'Дополнительные пользователи доступны только на Премиум тарифе'];
        }

        $priceData = $this->calculateUserPrice($userCount, $period);

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'company_id' => $company->id,
                'payment_type' => 'additional_users',
                'status' => 'pending',
                'amount' => $priceData['total'],
                'currency' => 'RUB',
                'period' => $period,
                'user_count' => $userCount,
                'payment_provider' => 'yookassa',
                'provider_payment_id' => $this->generatePaymentId(),
                'metadata' => array_merge($metadata, [
                    'price_per_user' => config('payment.price_per_user', 400),
                    'original_price' => $priceData['original'],
                    'discount' => $priceData['discount'],
                    'discount_percent' => $priceData['discount_percent'],
                ]),
            ]);
            DB::commit();

            $paymentData = $this->prepareAdditionalUsersPaymentData($payment, $company, $userCount, $priceData);
            $yooKassaResponse = $this->yooKassaApi->createPayment($paymentData);

            if (!$yooKassaResponse || !isset($yooKassaResponse['id'])) {
                $payment->update(['status' => 'failed']);
                return ['success' => false, 'error' => 'Ошибка создания платежа в YooKassa'];
            }

            $payment->update([
                'provider_payment_id' => $yooKassaResponse['id'],
                'provider_payment_url' => $yooKassaResponse['confirmation']['confirmation_url'] ?? null,
                'provider_data' => $yooKassaResponse,
            ]);

            return [
                'success' => true,
                'payment' => $payment,
                'payment_url' => $yooKassaResponse['confirmation']['confirmation_url'] ?? null,
                'amount' => $priceData['total'],
            ];
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Additional users payment creation failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /* =====================================================================
     |  Обработка успешных платежей
     ===================================================================== */

    public function handleSuccessfulPayment(Payment $payment, array $providerData): bool
    {
        if ($payment->isCompleted()) {
            return true;
        }

        DB::beginTransaction();
        try {
            $payment->markAsCompleted();
            $payment->update(['provider_data' => $providerData]);

            if ($payment->payment_type === 'subscription') {
                $this->activateSubscription($payment);
            } elseif ($payment->payment_type === 'additional_users') {
                $this->activateAdditionalUsers($payment);
            }

            DB::commit();

            Log::info('Payment processed successfully', [
                'payment_id' => $payment->id,
                'company_id' => $payment->company_id,
                'amount' => $payment->amount,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    protected function activateSubscription(Payment $payment): void
    {
        $company = $payment->company;
        $months = $this->getMonthsFromPeriod($payment->period);

        // 1) Деактивируем старые подписки, сохраняя активные покупки пользователей
        $activePurchases = collect();
        $oldSubscriptions = Subscription::where('company_id', $company->id)
            ->where('status', 'active')
            ->get();

        foreach ($oldSubscriptions as $oldSubscription) {
            $oldSubscription->update(['status' => 'expired']);

            $purchases = AdditionalUserPurchase::where('subscription_id', $oldSubscription->id)
                ->where('is_active', true)
                ->where('expires_at', '>', now())
                ->get();

            foreach ($purchases as $purchase) {
                $activePurchases->push($purchase);
            }

            AdditionalUserPurchase::where('subscription_id', $oldSubscription->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        // 2) Создаём новую подписку
        $subscription = Subscription::create([
            'company_id' => $company->id,
            'type' => 'premium',
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonths($months),
            'base_user_slots' => self::PREMIUM_BASE_USER_LIMIT,
            'storage_limit' => self::PREMIUM_STORAGE_LIMIT,
            'features' => $this->getPremiumFeatures(),
            'payment_method' => 'yookassa',
            'payment_provider_id' => $payment->provider_payment_id,
            'auto_renew' => false,
        ]);

        $payment->update(['subscription_id' => $subscription->id]);

        // 3) Переносим активные покупки доп. пользователей на новую подписку
        foreach ($activePurchases as $purchase) {
            AdditionalUserPurchase::create([
                'company_id' => $company->id,
                'subscription_id' => $subscription->id,
                'user_count' => $purchase->user_count,
                'period' => $purchase->period,
                'expires_at' => $purchase->expires_at,
                'is_active' => true,
            ]);

            $purchase->update(['is_active' => false]);

            Log::info('Additional users transferred to new subscription', [
                'old_purchase_id' => $purchase->id,
                'new_subscription_id' => $subscription->id,
                'user_count' => $purchase->user_count,
            ]);
        }

        // 4) Обновляем компанию и хранилище
        $company->update(['license_type' => 'premium']);
        $this->updateStorageLimit($company, $subscription->storage_limit);

        Log::info('Subscription activated', [
            'company_id' => $company->id,
            'subscription_id' => $subscription->id,
            'period' => $payment->period,
            'months' => $months,
            'transferred_purchases' => $activePurchases->count(),
        ]);
    }

    protected function activateAdditionalUsers(Payment $payment): void
    {
        $company = $payment->company;
        $subscription = Subscription::where('company_id', $company->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            throw new \Exception('Активная подписка не найдена');
        }

        $months = $this->getMonthsFromPeriod($payment->period);

        $additionalPurchase = AdditionalUserPurchase::create([
            'company_id' => $company->id,
            'subscription_id' => $subscription->id,
            'user_count' => $payment->user_count,
            'period' => $payment->period,
            'expires_at' => now()->addMonths($months),
            'is_active' => true,
        ]);

        Log::info('Additional users activated', [
            'subscription_id' => $subscription->id,
            'user_count' => $payment->user_count,
            'expires_at' => now()->addMonths($months),
            'purchase_id' => $additionalPurchase->id,
        ]);
    }

    /* =====================================================================
     |  Webhook / истечение подписок
     ===================================================================== */

    public function handleWebhook(array $data): array
    {
        if (!isset($data['object']['id'])) {
            return ['success' => false, 'error' => 'Invalid webhook data'];
        }

        $paymentObject = $data['object'];
        $payment = Payment::where('provider_payment_id', $paymentObject['id'])->first();

        if (!$payment) {
            return ['success' => false, 'error' => 'Payment not found'];
        }

        if (($paymentObject['status'] ?? null) === 'succeeded' && ($paymentObject['paid'] ?? false) === true) {
            $this->handleSuccessfulPayment($payment, $paymentObject);
            return ['success' => true, 'message' => 'Payment processed'];
        }

        if (($paymentObject['status'] ?? null) === 'canceled') {
            $payment->markAsFailed('Payment cancelled');
            return ['success' => true, 'message' => 'Payment cancelled'];
        }

        return ['success' => true, 'message' => 'Webhook received'];
    }

    public function checkExpiredSubscriptions(): int
    {
        $processed = 0;

        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            DB::beginTransaction();
            try {
                $company = $subscription->company;

                $subscription->update(['status' => 'expired']);

                AdditionalUserPurchase::where('subscription_id', $subscription->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

                $company->update(['license_type' => 'basic']);
                $this->updateStorageLimit($company, self::BASIC_STORAGE_LIMIT);

                DB::commit();
                $processed++;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to process expired subscription', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $processed;
    }

    /* =====================================================================
     |  Расчёт цен
     ===================================================================== */

    public function calculatePremiumPrice(string $period): array
    {
        $monthlyPrice = (float) config('payment.premium_monthly_price', 1490);
        $discounts = config('payment.discounts', ['6months' => 10, 'year' => 15]);

        $original = $monthlyPrice;
        $discountPercent = 0;

        switch ($period) {
            case '6months':
                $original = $monthlyPrice * 6;
                $discountPercent = (float) ($discounts['6months'] ?? 10);
                $total = $original * (1 - $discountPercent / 100);
                break;
            case 'year':
                $original = $monthlyPrice * 12;
                $discountPercent = (float) ($discounts['year'] ?? 15);
                $total = $original * (1 - $discountPercent / 100);
                break;
            default:
                $total = $original;
        }

        $monthsCount = $this->getMonthsFromPeriod($period);

        return [
            'original' => round($original, 2),
            'total' => round($total, 2),
            'discount' => round($original - $total, 2),
            'discount_percent' => $discountPercent,
            'price_per_month' => round($total / max($monthsCount, 1), 2),
        ];
    }

    public function calculateUserPrice(int $userCount, string $period): array
    {
        $pricePerUser = (float) config('payment.price_per_user', 400);
        $discounts = config('payment.discounts', ['6months' => 10, 'year' => 15]);

        $original = $userCount * $pricePerUser;
        $discountPercent = 0;
        $total = $original;

        switch ($period) {
            case '6months':
                $original = $userCount * $pricePerUser * 6;
                $discountPercent = (float) ($discounts['6months'] ?? 10);
                $total = $original * (1 - $discountPercent / 100);
                break;
            case 'year':
                $original = $userCount * $pricePerUser * 12;
                $discountPercent = (float) ($discounts['year'] ?? 15);
                $total = $original * (1 - $discountPercent / 100);
                break;
        }

        $monthsCount = $this->getMonthsFromPeriod($period);

        return [
            'original' => round($original, 2),
            'total' => round($total, 2),
            'discount' => round($original - $total, 2),
            'discount_percent' => $discountPercent,
            'price_per_user' => $pricePerUser,
            'price_per_month' => round($total / max($monthsCount, 1), 2),
        ];
    }

    /* =====================================================================
     |  Подготовка данных платежа (включая receipt)
     ===================================================================== */

    protected function preparePaymentData(Payment $payment, Company $company, array $priceData): array
    {
        $periodText = $this->getPeriodText($payment->period);
        $amountValue = number_format((float) $payment->amount, 2, '.', '');

        return [
            'amount' => [
                'value' => $amountValue,
                'currency' => 'RUB',
            ],
            'receipt' => $this->buildReceipt(
                $company,
                "Премиум подписка МенеджерПлюс на {$periodText}",
                $amountValue
            ),
            'payment_method_data' => [
                'type' => 'bank_card',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => url(config('payment.yookassa.return_url')),
            ],
            'description' => "Премиум подписка МенеджерПлюс на {$periodText}",
            'metadata' => [
                'payment_id' => $payment->id,
                'company_id' => $company->id,
                'payment_type' => 'subscription',
                'period' => $payment->period,
            ],
            'capture' => true,
        ];
    }

    protected function prepareAdditionalUsersPaymentData(Payment $payment, Company $company, int $userCount, array $priceData): array
    {
        $periodText = $this->getPeriodText($payment->period);
        $amountValue = number_format((float) $payment->amount, 2, '.', '');

        return [
            'amount' => [
                'value' => $amountValue,
                'currency' => 'RUB',
            ],
            'receipt' => $this->buildReceipt(
                $company,
                "Добавление {$userCount} пользователей в МенеджерПлюс на {$periodText}",
                $amountValue
            ),
            'payment_method_data' => [
                'type' => 'bank_card',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => url(config('payment.yookassa.return_url')),
            ],
            'description' => "Добавление {$userCount} пользователей в МенеджерПлюс на {$periodText}",
            'metadata' => [
                'payment_id' => $payment->id,
                'company_id' => $company->id,
                'payment_type' => 'additional_users',
                'user_count' => $userCount,
                'period' => $payment->period,
            ],
            'capture' => true,
        ];
    }

    /**
     * Собирает объект receipt для YooKassa.
     * Сумма позиции ВСЕГДА равна сумме платежа.
     */
    protected function buildReceipt(Company $company, string $description, string $amountValue): array
    {
        $email = $company->email
            ?? optional($company->user)->email
            ?? optional(auth()->user())->email
            ?? config('payment.receipt_fallback_email', 'noreply@example.com');

        return [
            'customer' => [
                'email' => $email,
            ],
            'items' => [
                [
                    'description' => mb_substr($description, 0, 128),
                    'quantity' => '1.00',
                    'amount' => [
                        'value' => $amountValue,
                        'currency' => 'RUB',
                    ],
                    'vat_code' => (int) config('payment.yookassa.vat_code', 1),
                    'payment_subject' => (string) config('payment.yookassa.payment_subject', 'service'),
                    'payment_mode' => (string) config('payment.yookassa.payment_mode', 'full_payment'),
                ],
            ],
        ];
    }

    /* =====================================================================
     |  Вспомогательные методы
     ===================================================================== */

    protected function updateStorageLimit(Company $company, int $newLimit): void
    {
        $storageUsage = StorageUsage::firstOrNew(['company_id' => $company->id]);
        $storageUsage->total_storage_limit = $newLimit;
        $storageUsage->license_type = $company->license_type;
        $storageUsage->save();
    }

    protected function generatePaymentId(): string
    {
        return 'pay_' . Str::random(16) . '_' . time();
    }

    protected function getPremiumFeatures(): array
    {
        return [
            'max_users' => self::PREMIUM_BASE_USER_LIMIT,
            'storage_tb' => 1,
            'priority_support' => true,
            'analytics' => true,
            'api_access' => true,
            'role_management' => true,
            'integrations' => true,
            'automation' => true,
            'calendar' => true,
            'mobile_app' => true,
            'export_data' => true,
        ];
    }

    protected function getPeriodText(string $period): string
    {
        return match ($period) {
            'month' => '1 месяц',
            '6months' => '6 месяцев',
            'year' => '12 месяцев',
            default => $period,
        };
    }

    protected function getMonthsFromPeriod(string $period): int
    {
        return match ($period) {
            'month' => 1,
            '6months' => 6,
            'year' => 12,
            default => 1,
        };
    }

    public function activateFromCallback(string $providerPaymentId): array
    {
        Log::info('Activating from callback', ['provider_payment_id' => $providerPaymentId]);

        $payment = Payment::where('provider_payment_id', $providerPaymentId)->first();

        if (!$payment) {
            return ['success' => false, 'error' => 'Payment not found'];
        }

        if ($payment->isCompleted()) {
            return ['success' => true, 'message' => 'Payment already completed'];
        }

        $paymentInfo = $this->yooKassaApi->getPayment($providerPaymentId);

        if ($paymentInfo && ($paymentInfo['status'] ?? null) === 'succeeded') {
            $this->handleSuccessfulPayment($payment, $paymentInfo);
            return ['success' => true, 'message' => 'Subscription activated'];
        }

        return ['success' => false, 'error' => 'Payment not succeeded'];
    }
}