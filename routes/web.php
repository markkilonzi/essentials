<?php

use Illuminate\Support\Facades\Route;
use Nyumbani\Essentials\Http\Controllers\DocsController;
use Nyumbani\Essentials\Http\Controllers\DocsDemoController;
use Nyumbani\Essentials\Http\Controllers\DocsSamplesController;
use Nyumbani\Essentials\Http\Controllers\ImageController;
use Nyumbani\Essentials\Http\Controllers\LeaveImpersonationController;
use Nyumbani\Essentials\Http\Controllers\WebManifestController;
use Nyumbani\Essentials\Http\Middleware\EnsureDocsAreAvailable;

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

//  Show the docs of every Nyumbani package to developers, in the environments the config allows, at /nyumbani-{package}/docs
Route::get('{package}/docs/{page?}', DocsController::class)
    ->where('package', 'nyumbani-[a-z0-9-]+')
    ->where('page', '[a-z-]+')
    ->middleware(['web', EnsureDocsAreAvailable::class])
    ->name('essentials.docs');

//  Run the interactive demos of the docs, which are available wherever the docs are
Route::post('{package}/docs/demo/{demo}', DocsDemoController::class)
    ->where('package', 'nyumbani-[a-z0-9-]+')
    ->where('demo', '[a-z0-9-]+')
    ->middleware(['web', EnsureDocsAreAvailable::class])
    ->name('essentials.docs.demo');

//  Serve the samples the docs run for real: a form that fills an error bag, remote modal content and the error pages
Route::prefix('{package}/docs/samples')
    ->where(['package' => 'nyumbani-[a-z0-9-]+'])
    ->middleware(['web', EnsureDocsAreAvailable::class])
    ->name('essentials.docs.samples.')
    ->group(function () {
        Route::post('form', [DocsSamplesController::class, 'form'])->name('form');
        Route::get('modal-content', [DocsSamplesController::class, 'modalContent'])->name('modal-content');
        Route::get('errors/{code}', [DocsSamplesController::class, 'error'])->whereIn('code', [401, 403, 404, 419, 429, 500, 503])->name('error');
    });
