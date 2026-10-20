<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The symbol used by the money() and currency() helpers.
    |
    */
    'currency' => env('ESSENTIALS_CURRENCY', 'KES'),

    /*
    |--------------------------------------------------------------------------
    | Fortify
    |--------------------------------------------------------------------------
    |
    | Login attempts allowed per minute for each username and IP address.
    |
    */
    'fortify' => [
        'max_attempts' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | App-wide behaviour. Set one to false to switch it off.
    |
    */
    'defaults' => [
        'prevent_lazy_loading' => true,
        'prohibit_destructive_commands' => true,
        'unwrapped_json_resources' => true,
        'debugbar' => true,
        'context' => true,
        'error_pages' => true,
        'messaging' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | The sentence shown under the app name on the sign-in page, and the colour
    | browsers use for the address bar and the installed app.
    |
    */
    'branding' => [
        'description' => env('ESSENTIALS_DESCRIPTION'),
        'theme_color' => env('ESSENTIALS_THEME_COLOR', '#d97706'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Messaging
    |--------------------------------------------------------------------------
    |
    | Every message goes out through Nyumbani Connect. Messages are queued, sent
    | at most rate_limit_per_second jobs a second, and retried when Connect fails.
    |
    */
    'messaging' => [
        'rate_limit_per_second' => 4,
        'connect' => [
            'base_url' => env('NYUMBANI_CONNECT_BASE_URL'),
            'access_token' => env('NYUMBANI_CONNECT_TOKEN'),
            'sms' => [
                'enabled' => env('ESSENTIALS_SMS_ENABLED', true),
                'sender_id' => env('ESSENTIALS_SMS_SENDER_ID', 'NYUMBANI'),
                'uri' => 'api/sms/send',
            ],
        ],
    ],
];
