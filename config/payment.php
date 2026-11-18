<?php
// config/payment.php

return [
    /*
    |--------------------------------------------------------------------------
    | Режим работы
    |--------------------------------------------------------------------------
    */
    'test_mode' => env('PAYMENT_TEST_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | YooKassa Конфигурация
    |--------------------------------------------------------------------------
    */
    'yookassa' => [
        'shop_id' => env('YOOKASSA_SHOP_ID'),
        'secret_key' => env('YOOKASSA_SECRET_KEY'),
        'return_url' => env('YOOKASSA_RETURN_URL', '/licence/payment/callback'),
        'webhook_url' => env('YOOKASSA_WEBHOOK_URL', '/licence/payment/webhook'),

        // Код ставки НДС для чека (receipt.items[].vat_code)
        // 1 = без НДС (УСН/патент), 2 = 0%, 3 = 10%, 4 = 20%
        // Уточните под свою систему налогообложения!
        'vat_code' => env('YOOKASSA_VAT_CODE', 1),

        // Предмет расчёта: service (услуга), commodity (товар) и т.д.
        'payment_subject' => env('YOOKASSA_PAYMENT_SUBJECT', 'service'),
        // Признак способа расчёта: full_payment (полный расчёт)
        'payment_mode' => env('YOOKASSA_PAYMENT_MODE', 'full_payment'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Цены и тарифы (в рублях)
    |--------------------------------------------------------------------------
    */
    'premium_monthly_price' => 1490,
    'price_per_user' => 400,

    'discounts' => [
        '6months' => 10,
        'year' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Email по умолчанию для чека, если у компании/пользователя нет email
    |--------------------------------------------------------------------------
    */
    'receipt_fallback_email' => env('PAYMENT_RECEIPT_FALLBACK_EMAIL', 'noreply@example.com'),
];