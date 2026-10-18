<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nyumbani Essentials
    |--------------------------------------------------------------------------
    |
    | Defaults shared by every Nyumbani app. Publish with the `essentials-config` tag to override.
    |
    */

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
    | Models
    |--------------------------------------------------------------------------
    |
    | The app's upload model, the one that uses HasUploads.
    |
    */
    'models' => [
        'upload' => 'App\Models\Upload',
    ],

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
    | prevent_lazy_loading: throw on lazy loading outside production
    | prohibit_destructive_commands: block migrate:fresh and the like in production
    | debugbar: enable the debugbar locally when installed
    | context: add the user, organization and request to the log context
    |
    */
    'defaults' => [
        'prevent_lazy_loading' => true,
        'prohibit_destructive_commands' => true,
        'debugbar' => true,
        'context' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Assets
    |--------------------------------------------------------------------------
    |
    | The Vite entries the package pages load. The app's css imports the package
    | theme: @import '../../vendor/nyumbani/essentials/resources/css/theme.css';
    |
    */
    'assets' => ['resources/css/app.css'],

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | The sentence shown under the app name on the sign-in page.
    |
    */
    'branding' => [
        'description' => env('ESSENTIALS_DESCRIPTION'),
    ],
];
