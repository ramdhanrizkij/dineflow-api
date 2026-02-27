<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuVariantRequest;
use App\Http\Requests\UpdateMenuVariantRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Menu;
use App\Models\MenuVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuVariantController extends Controller
{
    public function index(Menu $menu): JsonResponse
    {
        $variants = $menu->variants()->orderBy('name', 'asc')->get();

        return ApiResponse::success($variants, 'Variants retrieved successfully');
    }

    public function paginate(Request $request, Menu $menu): JsonResponse
    {
        $perPage   = (int) $request->query('per_page', 10);
        $search    = $request->query('search');
        $sort      = $request->query('sort', 'name');
        $direction = $request->query('sort_direction', 'asc');

        $allowedSorts = ['id', 'name', 'additional_price', 'is_default', 'created_at'];
        $sort         = in_array($sort, $allowedSorts) ? $sort : 'name';
        $direction    = in_array(strtolower($direction), ['asc', 'desc']) ? strtolower($direction) : 'asc';

        $query = $menu->variants();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $variants = $query->orderBy($sort, $direction)->paginate($perPage);

        return ApiResponse::paginated($variants, 'Variants retrieved successfully', [
            'sort'           => $sort,
            'sort_direction' => $direction,
        ]);
    }

    public function store(StoreMenuVariantRequest $request, Menu $menu): JsonResponse
    {
        $variant = $menu->variants()->create([
            'name'             => $request->name,
            'additional_price' => $request->additional_price,
            'is_default'       => $request->input('is_default', false),
        ]);

        return ApiResponse::created(
            $this->formatVariant($variant),
            'Variant created successfully'
        );
    }

    public function show(Menu $menu, MenuVariant $variant): JsonResponse
    {
        return ApiResponse::success(
            $this->formatVariant($variant),
            'Variant retrieved successfully'
        );
    }

    public function update(UpdateMenuVariantRequest $request, Menu $menu, MenuVariant $variant): JsonResponse
    {
        $variant->update([
            'name'             => $request->input('name', $variant->name),
            'additional_price' => $request->input('additional_price', $variant->additional_price),
            'is_default'       => $request->input('is_default', $variant->is_default),
        ]);

        return ApiResponse::success(
            $this->formatVariant($variant->fresh()),
            'Variant updated successfully'
        );
    }

    public function destroy(Menu $menu, MenuVariant $variant): JsonResponse
    {
        $variant->delete();

        return ApiResponse::deleted('Variant deleted successfully');
    }

    private function formatVariant(MenuVariant $variant): array
    {
        return [
            'id'               => $variant->id,
            'menu_id'          => $variant->menu_id,
            'name'             => $variant->name,
            'additional_price' => $variant->additional_price,
            'is_default'       => $variant->is_default,
            'created_at'       => $variant->created_at,
            'updated_at'       => $variant->updated_at,
        ];
    }
}
