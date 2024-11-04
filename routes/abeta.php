<?php

use Illuminate\Support\Facades\Route;
use AbetaIO\Laravel\Http\Controllers;

Route::prefix(config('abeta.routes.prefix'))
    ->name('abeta.')
    ->group(function () {
        Route::post('setup-request', [Controllers\PunchOutController::class, 'setupRequest'])->name('setupRequest');
        Route::middleware(['web'])->get('login', [Controllers\LoginController::class, 'login'])->name('login');
    });