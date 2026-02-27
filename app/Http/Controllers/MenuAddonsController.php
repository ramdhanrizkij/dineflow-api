<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuAddonsRequest;
use App\Http\Requests\UpdateMenuAddonsRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Menu;
use App\Models\MenuAddons;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuAddonsController extends Controller
{
    public function index(Menu $menu): JsonResponse
    {
        $addons = $menu->addons()->orderBy('name', 'asc')->get();

        return ApiResponse::success($addons, 'Addons retrieved successfully');
    }

    public function paginate(Request $request, Menu $menu): JsonResponse
    {
        $perPage   = (int) $request->query('per_page', 10);
        $search    = $request->query('search');
        $sort      = $request->query('sort', 'name');
        $direction = $request->query('sort_direction', 'asc');

        $allowedSorts = ['id', 'name', 'price', 'is_required', 'created_at'];
        $sort         = in_array($sort, $allowedSorts) ? $sort : 'name';
        $direction    = in_array(strtolower($direction), ['asc', 'desc']) ? strtolower($direction) : 'asc';

        $query = $menu->addons();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $addons = $query->orderBy($sort, $direction)->paginate($perPage);

        return ApiResponse::paginated($addons, 'Addons retrieved successfully', [
            'sort'           => $sort,
            'sort_direction' => $direction,
        ]);
    }

    public function store(StoreMenuAddonsRequest $request, Menu $menu): JsonResponse
    {
        $addon = $menu->addons()->create([
            'name'        => $request->name,
            'price'       => $request->price,
            'is_required' => $request->input('is_required', false),
        ]);

        return ApiResponse::created(
            $this->formatAddon($addon),
            'Addon created successfully'
        );
    }

    public function show(Menu $menu, MenuAddons $addon): JsonResponse
    {
        return ApiResponse::success(
            $this->formatAddon($addon),
            'Addon retrieved successfully'
        );
    }

    public function update(UpdateMenuAddonsRequest $request, Menu $menu, MenuAddons $addon): JsonResponse
    {
        $addon->update([
            'name'        => $request->input('name', $addon->name),
            'price'       => $request->input('price', $addon->price),
            'is_required' => $request->input('is_required', $addon->is_required),
        ]);

        return ApiResponse::success(
            $this->formatAddon($addon->fresh()),
            'Addon updated successfully'
        );
    }

    public function destroy(Menu $menu, MenuAddons $addon): JsonResponse
    {
        $addon->delete();

        return ApiResponse::deleted('Addon deleted successfully');
    }

    private function formatAddon(MenuAddons $addon): array
    {
        return [
            'id'          => $addon->id,
            'menu_id'     => $addon->menu_id,
            'name'        => $addon->name,
            'price'       => $addon->price,
            'is_required' => $addon->is_required,
            'created_at'  => $addon->created_at,
            'updated_at'  => $addon->updated_at,
        ];
    }
}
