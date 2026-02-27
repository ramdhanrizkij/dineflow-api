<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuVariantController;
use App\Http\Controllers\MenuAddonsController;

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('menus')->group(function () {
        Route::get('/', [MenuController::class, 'index']);
        Route::get('/paginate', [MenuController::class, 'paginate']);
        Route::post('/', [MenuController::class, 'store']);

        Route::prefix('/{menu}')->group(function () {
            Route::get('/', [MenuController::class, 'show']);
            Route::put('/', [MenuController::class, 'update']);
            Route::delete('/', [MenuController::class, 'destroy']);

            // Variants
            Route::get('/variants', [MenuVariantController::class, 'index']);
            Route::get('/variants/paginate', [MenuVariantController::class, 'paginate']);
            Route::post('/variants', [MenuVariantController::class, 'store']);
            Route::get('/variants/{variant}', [MenuVariantController::class, 'show']);
            Route::put('/variants/{variant}', [MenuVariantController::class, 'update']);
            Route::delete('/variants/{variant}', [MenuVariantController::class, 'destroy']);

            // Addons
            Route::get('/addons', [MenuAddonsController::class, 'index']);
            Route::get('/addons/paginate', [MenuAddonsController::class, 'paginate']);
            Route::post('/addons', [MenuAddonsController::class, 'store']);
            Route::get('/addons/{addon}', [MenuAddonsController::class, 'show']);
            Route::put('/addons/{addon}', [MenuAddonsController::class, 'update']);
            Route::delete('/addons/{addon}', [MenuAddonsController::class, 'destroy']);
        });
    });
});
