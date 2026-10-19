<?php

use Illuminate\Support\Facades\Route;
use Nyumbani\Essentials\Http\Controllers\ImageController;
use Nyumbani\Essentials\Http\Controllers\LeaveImpersonationController;
use Nyumbani\Essentials\Http\Controllers\WebManifestController;

//  Serve the package images, which are static so they need no session
Route::get('essentials/images/{image}', ImageController::class)
    ->where('image', '[A-Za-z0-9._-]+')
    ->name('essentials.image');

//  Describe the installable app, named after the app that uses the package
Route::get('essentials/site.webmanifest', WebManifestController::class)
    ->name('essentials.manifest');

//  End an impersonation, which only signed-in users can reach
Route::post('essentials/impersonation/leave', LeaveImpersonationController::class)
    ->middleware(['web', 'auth'])
    ->name('essentials.impersonation.leave');
