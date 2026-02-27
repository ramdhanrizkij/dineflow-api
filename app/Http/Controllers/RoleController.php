<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')->get();

        return ApiResponse::success($roles, 'Roles retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|unique:roles']);

        $role = Role::create(['name' => $request->name]);

        if ($request->permissions) {
            $role->givePermissionTo($request->permissions);
        }

        return ApiResponse::created($role->load('permissions'), 'Role created successfully');
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        $request->validate(['name' => 'required|string|unique:roles,name,' . $role->id]);

        $role->update(['name' => $request->name]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return ApiResponse::success($role->load('permissions'), 'Role updated successfully');
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return ApiResponse::deleted('Role deleted successfully');
    }

    public function assignRole(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|exists:roles,name',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $user->assignRole($request->role);

        return ApiResponse::success(null, "Role '{$request->role}' assigned successfully");
    }

    public function revokeRole(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|exists:roles,name',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $user->removeRole($request->role);

        return ApiResponse::success(null, "Role '{$request->role}' revoked successfully");
    }
}