<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        $menus = Menu::with('category')
            ->orderBy('name', 'asc')
            ->get()
            ->map(fn(Menu $menu) => $this->formatMenu($menu));

        return ApiResponse::success($menus, 'Menus retrieved successfully');
    }

    public function paginate(Request $request): JsonResponse
    {
        $perPage    = (int) $request->query('per_page', 10);
        $search     = $request->query('search');
        $isActive   = $request->query('is_active');
        $categoryId = $request->query('category_id');
        $sort       = $request->query('sort', 'name');
        $direction  = $request->query('sort_direction', 'asc');

        $allowedSorts = ['id', 'name', 'base_price', 'is_active', 'created_at'];
        $sort         = in_array($sort, $allowedSorts) ? $sort : 'name';
        $direction    = in_array(strtolower($direction), ['asc', 'desc']) ? strtolower($direction) : 'asc';

        $query = Menu::with('category');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if (!is_null($isActive)) {
            $query->where('is_active', filter_var($isActive, FILTER_VALIDATE_BOOLEAN));
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $menus = $query->orderBy($sort, $direction)->paginate($perPage);

        return ApiResponse::paginated($menus, 'Menus retrieved successfully', [
            'sort'           => $sort,
            'sort_direction' => $direction,
        ]);
    }

    public function store(StoreMenuRequest $request): JsonResponse
    {
        $menu = Menu::create([
            'name'        => $request->name,
            'description' => $request->description,
            'base_price'  => $request->base_price,
            'category_id' => $request->category_id,
            'is_active'   => $request->input('is_active', true),
        ]);

        return ApiResponse::created(
            $this->formatMenu($menu->load('category')),
            'Menu created successfully'
        );
    }

    public function show(Menu $menu): JsonResponse
    {
        return ApiResponse::success(
            $this->formatMenu($menu->load('category')),
            'Menu retrieved successfully'
        );
    }

    public function update(UpdateMenuRequest $request, Menu $menu): JsonResponse
    {
        $menu->update([
            'name'        => $request->input('name', $menu->name),
            'description' => $request->input('description', $menu->description),
            'base_price'  => $request->input('base_price', $menu->base_price),
            'category_id' => $request->input('category_id', $menu->category_id),
            'is_active'   => $request->input('is_active', $menu->is_active),
        ]);

        return ApiResponse::success(
            $this->formatMenu($menu->fresh()->load('category')),
            'Menu updated successfully'
        );
    }

    public function destroy(Menu $menu): JsonResponse
    {
        $menu->delete();

        return ApiResponse::deleted('Menu deleted successfully');
    }

    private function formatMenu(Menu $menu): array
    {
        return [
            'id'          => $menu->id,
            'name'        => $menu->name,
            'description' => $menu->description,
            'base_price'  => $menu->base_price,
            'is_active'   => $menu->is_active,
            'category'    => $menu->relationLoaded('category') ? [
                'id'   => $menu->category?->id,
                'name' => $menu->category?->name,
            ] : null,
            'created_at'  => $menu->created_at,
            'updated_at'  => $menu->updated_at,
        ];
    }
}
