<?php

use Illuminate\Support\Facades\Route;
use AbetaIO\Laravel\Http\Controllers;

Route::prefix(config('abeta.routes.prefix'))
    ->name('abeta.')
    ->middleware('abeta_session')
    ->group(function () {
        Route::post('setup-request', [Controllers\PunchOutController::class, 'setupRequest'])->name('setupRequest');
        Route::get('login', [Controllers\LoginController::class, 'login'])->name('login');
        Route::post('order-confrimation', [Controllers\OrderController::class, 'confirm'])->name('order.confirm');
    });