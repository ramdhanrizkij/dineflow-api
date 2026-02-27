<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);

        Route::get('/{order}', [OrderController::class, 'show']);
        Route::put('/{order}', [OrderController::class, 'update']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);

        Route::get('/{order}/status-logs', [OrderController::class, 'statusLogs']);

        Route::get('/{order}/payments', [OrderController::class, 'payments']);
        Route::post('/{order}/payments', [OrderController::class, 'storePayment']);
    });
});
