<?php

use Illuminate\Support\Facades\Route;
use Nyumbani\Essentials\Http\Controllers\ImageController;

//  Serve the package images, which are static so they need no session
Route::get('essentials/images/{image}', ImageController::class)
    ->where('image', '[A-Za-z0-9._-]+')
    ->name('essentials.image');
