<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::select('id', 'name', 'description', 'is_active', 'created_at', 'updated_at')
            ->orderBy('name', 'asc')
            ->get();

        return ApiResponse::success($categories, 'Categories retrieved successfully');
    }

    public function paginate(Request $request): JsonResponse
    {
        $perPage   = (int) $request->query('per_page', 10);
        $search    = $request->query('search');
        $isActive  = $request->query('is_active');
        $sort      = $request->query('sort', 'created_at');
        $direction = $request->query('sort_direction', 'asc');

        $allowedSorts = ['id', 'name', 'is_active', 'created_at'];
        $sort         = in_array($sort, $allowedSorts) ? $sort : 'created_at';
        $direction    = in_array(strtolower($direction), ['asc', 'desc']) ? strtolower($direction) : 'asc';

        $query = Category::select('id', 'name', 'description', 'is_active', 'created_at', 'updated_at');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if (!is_null($isActive)) {
            $query->where('is_active', filter_var($isActive, FILTER_VALIDATE_BOOLEAN));
        }

        $categories = $query->orderBy($sort, $direction)->paginate($perPage);

        return ApiResponse::paginated($categories, 'Categories retrieved successfully', [
            'sort'           => $sort,
            'sort_direction' => $direction,
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->input('is_active', true),
        ]);

        return ApiResponse::created(
            $this->formatCategory($category),
            'Category created successfully'
        );
    }

    public function show(Category $category): JsonResponse
    {
        return ApiResponse::success(
            $this->formatCategory($category),
            'Category retrieved successfully'
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->input('is_active', $category->is_active),
        ]);

        return ApiResponse::success(
            $this->formatCategory($category),
            'Category updated successfully'
        );
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return ApiResponse::deleted('Category deleted successfully');
    }

    private function formatCategory(Category $category): array
    {
        return [
            'id'          => $category->id,
            'name'        => $category->name,
            'description' => $category->description,
            'is_active'   => $category->is_active,
            'created_at'  => $category->created_at,
            'updated_at'  => $category->updated_at,
        ];
    }
}
