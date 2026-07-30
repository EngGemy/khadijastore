<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Web Push (VAPID)
    |--------------------------------------------------------------------------
    |
    | Generate keys:
    |   php artisan webpush:vapid
    | Or via vendor binary after composer require minishlink/web-push.
    |
    */
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', env('APP_URL', 'mailto:admin@example.com')),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
    ],

    'enabled' => (bool) env('WEBPUSH_ENABLED', true),
];
