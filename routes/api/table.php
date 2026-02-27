<?php

use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('tables')->group(function () {
        Route::get('/', [TableController::class, 'index']);
        Route::post('/', [TableController::class, 'store']);
        Route::get('/paginate', [TableController::class, 'paginate']);
        Route::get('/{table}', [TableController::class, 'show']);
        Route::put('/{table}', [TableController::class, 'update']);
        Route::delete('/{table}', [TableController::class, 'destroy']);
    });
});
