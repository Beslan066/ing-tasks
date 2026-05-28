<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\AdditionalUserPurchase;
use App\Models\StorageUsage;
use App\Services\YooKassaApiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    protected $yooKassaApi;
    protected $testMode;

    // Цены
    const PREMIUM_MONTHLY_PRICE = 2490;
    const PRICE_PER_USER = 400;

    // Скидки
    const DISCOUNT_6_MONTHS = 10;
    const DISCOUNT_12_MONTHS = 15;

    // Лимиты
    const BASIC_USER_LIMIT = 5;
    const PREMIUM_BASE_USER_LIMIT = 15;

    public function __construct(YooKassaApiService $yooKassaApi)
    {
        $this->yooKassaApi = $yooKassaApi;
        $this->testMode = config('payment.test_mode', true);
    }

    /**
     * Создание платежа за премиум подписку
     */
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
                    'discount_percent' => $priceData['discount_percent']
                ])
            ]);

            DB::commit();

            // Создаем платеж в YooKassa
            $paymentData = $this->preparePaymentData($payment, $company, $priceData);
            $yooKassaResponse = $this->yooKassaApi->createPayment($paymentData);

            if (!$yooKassaResponse || !isset($yooKassaResponse['id'])) {
                $payment->update(['status' => 'failed']);
                return ['success' => false, 'error' => 'Ошибка создания платежа в YooKassa'];
            }

            // Обновляем платеж с данными от YooKassa
            $payment->update([
                'provider_payment_id' => $yooKassaResponse['id'],
                'provider_payment_url' => $yooKassaResponse['confirmation']['confirmation_url'] ?? null,
                'provider_data' => $yooKassaResponse
            ]);

            return [
                'success' => true,
                'payment' => $payment,
                'payment_url' => $yooKassaResponse['confirmation']['confirmation_url'],
                'amount' => $priceData['total']
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Premium payment creation failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Создание платежа за дополнительных пользователей
     */
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
                    'price_per_user' => self::PRICE_PER_USER,
                    'original_price' => $priceData['original'],
                    'discount' => $priceData['discount'],
                    'discount_percent' => $priceData['discount_percent']
                ])
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
                'provider_data' => $yooKassaResponse
            ]);

            return [
                'success' => true,
                'payment' => $payment,
                'payment_url' => $yooKassaResponse['confirmation']['confirmation_url'],
                'amount' => $priceData['total']
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Additional users payment creation failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Обработка успешного платежа
     */
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
                'amount' => $payment->amount
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Активация премиум подписки
     */
    protected function activateSubscription(Payment $payment): void
    {
        $company = $payment->company;
        $period = $payment->period;
        $months = $this->getMonthsFromPeriod($period);

        // Деактивируем старые подписки
        Subscription::where('company_id', $company->id)
            ->where('status', 'active')
            ->each(function ($subscription) {
                $subscription->update(['status' => 'expired']);
            });

        // Деактивируем старые покупки пользователей
        AdditionalUserPurchase::where('company_id', $company->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Создаем новую подписку
        $subscription = Subscription::create([
            'company_id' => $company->id,
            'type' => 'premium',
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonths($months),
            'base_user_slots' => self::PREMIUM_BASE_USER_LIMIT,
            'storage_limit' => 1073741824000,
            'features' => $this->getPremiumFeatures(),
            'payment_method' => 'yookassa',
            'payment_provider_id' => $payment->provider_payment_id,
            'auto_renew' => false
        ]);

        $payment->update(['subscription_id' => $subscription->id]);

        // Обновляем компанию
        $company->update(['license_type' => 'premium']);

        // Обновляем хранилище
        $this->updateStorageLimit($company, $subscription->storage_limit);
    }

    /**
     * Активация дополнительных пользователей
     */
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

        AdditionalUserPurchase::create([
            'company_id' => $company->id,
            'subscription_id' => $subscription->id,
            'user_count' => $payment->user_count,
            'period' => $payment->period,
            'expires_at' => now()->addMonths($months),
            'is_active' => true
        ]);
    }

    /**
     * Обработка вебхука от YooKassa
     */
    public function handleWebhook(array $data): array
    {
        if (!isset($data['object']) || !isset($data['object']['id'])) {
            return ['success' => false, 'error' => 'Invalid webhook data'];
        }

        $paymentObject = $data['object'];
        $payment = Payment::where('provider_payment_id', $paymentObject['id'])->first();

        if (!$payment) {
            return ['success' => false, 'error' => 'Payment not found'];
        }

        if ($paymentObject['status'] === 'succeeded' && $paymentObject['paid'] === true) {
            $this->handleSuccessfulPayment($payment, $paymentObject);
            return ['success' => true, 'message' => 'Payment processed'];
        }

        if ($paymentObject['status'] === 'canceled') {
            $payment->markAsFailed('Payment cancelled');
            return ['success' => true, 'message' => 'Payment cancelled'];
        }

        return ['success' => true, 'message' => 'Webhook received'];
    }

    /**
     * Проверка и обработка истекших подписок
     */
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

                $this->updateStorageLimit($company, 1073741824); // 1GB

                DB::commit();
                $processed++;

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to process expired subscription', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $processed;
    }

    /**
     * Расчет цены премиум подписки
     */
    public function calculatePremiumPrice(string $period): array
    {
        $monthlyPrice = self::PREMIUM_MONTHLY_PRICE;
        $original = $monthlyPrice;
        $discountPercent = 0;

        switch ($period) {
            case '6months':
                $original = $monthlyPrice * 6;
                $discountPercent = self::DISCOUNT_6_MONTHS;
                $total = $original * (1 - $discountPercent / 100);
                break;
            case 'year':
                $original = $monthlyPrice * 12;
                $discountPercent = self::DISCOUNT_12_MONTHS;
                $total = $original * (1 - $discountPercent / 100);
                break;
            default:
                $total = $original;
        }

        return [
            'original' => $original,
            'total' => round($total),
            'discount' => round($original - $total),
            'discount_percent' => $discountPercent,
            'price_per_month' => round($total / ($period === 'month' ? 1 : ($period === '6months' ? 6 : 12)))
        ];
    }

    /**
     * Расчет цены за дополнительных пользователей
     */
    public function calculateUserPrice(int $userCount, string $period): array
    {
        $pricePerUser = self::PRICE_PER_USER;
        $original = $userCount * $pricePerUser;
        $discountPercent = 0;
        $total = $original;

        switch ($period) {
            case '6months':
                $original = $userCount * $pricePerUser * 6;
                $discountPercent = self::DISCOUNT_6_MONTHS;
                $total = $original * (1 - $discountPercent / 100);
                break;
            case 'year':
                $original = $userCount * $pricePerUser * 12;
                $discountPercent = self::DISCOUNT_12_MONTHS;
                $total = $original * (1 - $discountPercent / 100);
                break;
        }

        return [
            'original' => round($original),
            'total' => round($total),
            'discount' => round($original - $total),
            'discount_percent' => $discountPercent,
            'price_per_user' => $pricePerUser,
            'price_per_month' => round($total / ($period === 'month' ? 1 : ($period === '6months' ? 6 : 12)))
        ];
    }

    /**
     * Подготовка данных для платежа в YooKassa
     */
    protected function preparePaymentData(Payment $payment, Company $company, array $priceData): array
    {
        $periodText = $this->getPeriodText($payment->period);

        return [
            'amount' => [
                'value' => (string)$payment->amount,
                'currency' => 'RUB'
            ],
            'payment_method_data' => [
                'type' => 'bank_card'
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => url(config('payment.yookassa.return_url'))
            ],
            'description' => "Премиум подписка МенеджерПлюс на {$periodText}",
            'metadata' => [
                'payment_id' => $payment->id,
                'company_id' => $company->id,
                'payment_type' => 'subscription',
                'period' => $payment->period
            ],
            'capture' => true
        ];
    }

    protected function prepareAdditionalUsersPaymentData(Payment $payment, Company $company, int $userCount, array $priceData): array
    {
        $periodText = $this->getPeriodText($payment->period);

        return [
            'amount' => [
                'value' => (string)$payment->amount,
                'currency' => 'RUB'
            ],
            'payment_method_data' => [
                'type' => 'bank_card'
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => url(config('payment.yookassa.return_url'))
            ],
            'description' => "Добавление {$userCount} пользователей в МенеджерПлюс на {$periodText}",
            'metadata' => [
                'payment_id' => $payment->id,
                'company_id' => $company->id,
                'payment_type' => 'additional_users',
                'user_count' => $userCount,
                'period' => $payment->period
            ],
            'capture' => true
        ];
    }

    /**
     * Обновление лимита хранилища
     */
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
            'export_data' => true
        ];
    }

    protected function getPeriodText(string $period): string
    {
        return match($period) {
            'month' => '1 месяц',
            '6months' => '6 месяцев',
            'year' => '12 месяцев',
            default => $period
        };
    }

    protected function getMonthsFromPeriod(string $period): int
    {
        return match($period) {
            'month' => 1,
            '6months' => 6,
            'year' => 12,
            default => 1
        };
    }
}
