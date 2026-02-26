<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json([
            'status'=>true,
            'message'=>'Roles retrieved successfully',
            'data'=>$roles
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles']);

        $role = Role::create(['name' => $request->name]);

        if ($request->permissions) {
            $role->givePermissionTo($request->permissions);
        }

        return response()->json([
            'status'=>true,
            'message'=>'Role created successfully',
            'data'=>$role->load('permissions')
        ],201);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate(['name' => 'required|string|unique:roles,name,' . $role->id]);

        $role->update(['name' => $request->name]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'status'=>true,
            'message'=>'Role updated successfully',
            'data'=>$role->load('permissions')
        ]);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json([
            'status'=>true,
            'message'=>'Role deleted successfully'
        ]);
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|exists:roles,name',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $user->assignRole($request->role);

        return response()->json([
            'status'=>true,
            'message'=>"Role '{$request->role}' assigned successfully"
        ]);
    }

    public function revokeRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|exists:roles,name',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $user->removeRole($request->role);

        return response()->json([
            'status'=>true,
            'message'=>"Role '{$request->role}' revoked successfully"
        ]);
    }
}