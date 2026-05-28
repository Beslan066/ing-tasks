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
    ],

    /*
    |--------------------------------------------------------------------------
    | Цены и тарифы
    |--------------------------------------------------------------------------
    */
    'premium_monthly_price' => 2490,
    'price_per_user' => 600,

    'discounts' => [
        '6months' => 10,
        'year' => 15,
    ],
];
