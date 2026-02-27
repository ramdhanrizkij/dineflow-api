<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        $permissions = Permission::all();

        return ApiResponse::success($permissions, 'Permissions retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|unique:permissions']);

        $permission = Permission::create(['name' => $request->name]);

        return ApiResponse::created($permission, 'Permission created successfully');
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return ApiResponse::deleted('Permission deleted successfully');
    }
}