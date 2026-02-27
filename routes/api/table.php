<?php

use App\Http\Controllers\TableController;
use App\Http\Controllers\TableCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('tables')->group(function () {
    Route::get('/', [TableController::class, 'index']);
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/', [TableController::class, 'store']);
        Route::get('/paginate', [TableController::class, 'paginate']);
        Route::get('/{table}', [TableController::class, 'show']);
        Route::put('/{table}', [TableController::class, 'update']);
        Route::delete('/{table}', [TableController::class, 'destroy']);
    });
});

Route::prefix('table-categories')->group(function(){
    Route::get('/', [TableCategoryController::class, 'index']);
        Route::post('/',[TableCategoryController::class, 'store'])->middleware('auth:api');
        Route::get('/paginate', [TableCategoryController::class, 'paginate'])->middleware('auth:api');
        Route::get('/{tableCategory}', [TableCategoryController::class, 'show']);
        Route::put('/{tableCategory}', [TableCategoryController::class, 'update'])->middleware('auth:api');
        Route::delete('/{tableCategory}', [TableCategoryController::class, 'destroy'])->middleware('auth:api');
});
