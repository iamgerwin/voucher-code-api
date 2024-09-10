<?php

use App\Http\Controllers\Api\AuthController;
//use App\Http\Controllers\Api\VoucherController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::group(['prefix' => 'auth', 'as' => 'auth.'], function() {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
})->middleware('api');

// Voucher routes
Route::group(['prefix' => 'vouchers', 'as' => 'vouchers.'], function() {
    Route::get('/', [VoucherController::class, 'index'])->name('index');
    Route::post('/', [VoucherController::class, 'store'])->name('store');
    Route::get('/{key}/{value}', [VoucherController::class, 'show'])->name('show');
    Route::delete('/{value}', [VoucherController::class, 'destroy'])->name('destroy');
    Route::delete('/{key}/{value', [VoucherController::class, 'destroyByKey'])->name('destroy.key');
})->middleware('auth:sanctum');
