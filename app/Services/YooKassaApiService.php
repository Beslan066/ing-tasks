<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YooKassaApiService
{
    protected string $shopId;
    protected string $secretKey;
    protected bool $testMode;
    protected string $apiUrl;

    public function __construct()
    {
        $this->testMode = (bool) config('payment.test_mode', true);
        $this->shopId = (string) config('payment.yookassa.shop_id');
        $this->secretKey = (string) config('payment.yookassa.secret_key');
        $this->apiUrl = 'https://api.yookassa.ru/v3/';
    }

    protected function getAuthHeader(): string
    {
        return 'Basic ' . base64_encode($this->shopId . ':' . $this->secretKey);
    }

    /**
     * Создание платежа в YooKassa.
     * Ожидает уже полностью подготовленный массив (включая receipt).
     */
    public function createPayment(array $data): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
                'Idempotence-Key' => $data['idempotence_key'] ?? uniqid('pay_', true),
            ])->post($this->apiUrl . 'payments', $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('YooKassa API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'request' => $data,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('YooKassa request failed', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function getPayment(string $paymentId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
            ])->get($this->apiUrl . 'payments/' . $paymentId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('YooKassa getPayment non-2xx', [
                'payment_id' => $paymentId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get payment info', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}