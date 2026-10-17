<?php

use Nyumbani\Essentials\Models\Upload;

return [
    /*
    |--------------------------------------------------------------------------
    | Nyumbani Essentials
    |--------------------------------------------------------------------------
    |
    | Default settings shared by every Nyumbani app. An app overrides them by
    | publishing this file (tag `essentials-config`).
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The currency symbol used by the money() and currency() helpers.
    |
    */
    'currency' => env('ESSENTIALS_CURRENCY', 'KES'),

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | The app models the package traits relate to.
    |
    */
    'models' => [
        'upload' => Upload::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Enums
    |--------------------------------------------------------------------------
    |
    | The app enums the package models cast to. Set upload_type to your app's
    | UploadType enum to cast the type column of an upload.
    |
    */
    'enums' => [
        'upload_type' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fortify
    |--------------------------------------------------------------------------
    |
    | Defaults for the sign-in screens: the view folder holding login,
    | forgot-password and reset-password, and how many login attempts are
    | allowed per minute for each username and IP address.
    |
    */
    'fortify' => [
        'views' => 'auth.pages',
        'max_attempts' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | App-wide behaviour every Nyumbani app shares. Switch one off by setting
    | it to false: lazy loading is prevented outside production, destructive
    | database commands are prohibited in production, and the debugbar is
    | enabled locally when it is installed, and the signed-in user, their
    | organization and the request payload are added to the log context.
    |
    */
    'defaults' => [
        'prevent_lazy_loading' => true,
        'prohibit_destructive_commands' => true,
        'debugbar' => true,
        'context' => true,
    ],
];
