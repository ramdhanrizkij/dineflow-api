<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::prefix('v1')->group(function () {
    require __DIR__ . '/api/auth.php';
    require __DIR__ . '/api/rbac.php';
    require __DIR__ . '/api/user.php';
    require __DIR__ . '/api/table.php';
    require __DIR__ . '/api/category.php';
    require __DIR__ . '/api/menu.php';
});
