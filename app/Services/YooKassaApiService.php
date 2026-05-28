<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YooKassaApiService
{
    protected $shopId;
    protected $secretKey;
    protected $testMode;
    protected $apiUrl;

    public function __construct()
    {
        $this->testMode = config('payment.test_mode', true);
        $this->shopId = config('payment.yookassa.shop_id');
        $this->secretKey = config('payment.yookassa.secret_key');
        $this->apiUrl = 'https://api.yookassa.ru/v3/';
    }

    protected function getAuthHeader(): string
    {
        return 'Basic ' . base64_encode($this->shopId . ':' . $this->secretKey);
    }

    public function createPayment(array $data): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json',
                'Idempotence-Key' => uniqid('pay_', true)
            ])->post($this->apiUrl . 'payments', $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('YooKassa API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('YooKassa request failed', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getPayment(string $paymentId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthHeader(),
                'Content-Type' => 'application/json'
            ])->get($this->apiUrl . 'payments/' . $paymentId);

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Failed to get payment info', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
