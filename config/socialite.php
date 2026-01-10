<?php

return [
    'providers' => [
        'vkontakte' => [
            'client_id' => env('VKONTAKTE_CLIENT_ID'),
            'client_secret' => env('VKONTAKTE_CLIENT_SECRET'),
            'redirect' => env('VKONTAKTE_REDIRECT_URI'),
        ],
        'yandex' => [
            'client_id' => env('YANDEX_CLIENT_ID'),
            'client_secret' => env('YANDEX_CLIENT_SECRET'),
            'redirect' => env('YANDEX_REDIRECT_URI'),
        ],
    ],
];
