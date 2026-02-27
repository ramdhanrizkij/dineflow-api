<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $search  = $request->query('search');

        $query = User::with('roles');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $users->through(fn ($user) => [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->roles->first()?->name,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);

        return ApiResponse::paginated($users, 'Users retrieved successfully');
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->syncRoles([$request->role]);

        return ApiResponse::created([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->roles->first()?->name,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 'User created successfully');
    }

    public function show(User $user): JsonResponse
    {
        return ApiResponse::success([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->roles->first()?->name,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 'User retrieved successfully');
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

        $user->load('roles');

        return ApiResponse::success([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->roles->first()?->name,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 'User updated successfully');
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return ApiResponse::deleted('User deleted successfully');
    }

    public function roles(): JsonResponse
    {
        $roles = Role::select('id', 'name')->get();

        return ApiResponse::success($roles, 'Roles retrieved successfully');
    }
}
