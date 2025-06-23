<?php

declare(strict_types=1);

use AbetaIO\Laravel\Http\Controllers;
use Illuminate\Support\Facades\Route;
// Comment for tet purposes

Route::prefix(config('abeta.routes.prefix'))
    ->name('abeta.')
    ->middleware('abeta_session')
    ->group(function () {
        Route::post('setup-request', [Controllers\PunchOutController::class, 'setupRequest'])->name('setupRequest');
        Route::get('login', [Controllers\LoginController::class, 'login'])->name('login');
        Route::post('order-confirmation', [Controllers\OrderController::class, 'confirm'])->name('order.confirm');
    });
