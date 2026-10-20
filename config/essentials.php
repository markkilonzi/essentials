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
];
