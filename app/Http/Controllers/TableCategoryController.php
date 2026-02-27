<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\TableCategory;
use App\Http\Requests\StoreTableCategoryRequest;
use App\Http\Requests\UpdateTableCategoryRequest;
use App\Http\Responses\ApiResponse;

class TableCategoryController extends Controller
{
    function index() : JsonResponse {
        $tableCategories = TableCategory::select('id', 'name','description', 'created_at', 'updated_at')
            ->orderBy('created_at', 'asc')
            ->get();

        return ApiResponse::success($tableCategories, 'Table categories retrieved successfully');
    }

    function paginate(Request $request) : JsonResponse {    
        $perPage = (int) $request->query('per_page', 10);
        $search = $request->query('search');
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('sort_direction', 'asc');

        $allowedSorts = ['id', 'name', 'created_at'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'created_at';
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? strtolower($direction) : 'asc';

        $query = TableCategory::select('id', 'name', 'description', 'created_at', 'updated_at');

        if($search){
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tableCategories = $query->orderBy($sort, $direction)->paginate($perPage);

        return ApiResponse::paginated($tableCategories, 'Table categories retrieved successfully', [
            'sort' => $sort,
            'sort_direction' => $direction,
        ]);
    }

    function show(TableCategory $tableCategory) : JsonResponse {
        return ApiResponse::success($tableCategory, 'Table category retrieved successfully');
    }

    function store(StoreTableCategoryRequest $request) : JsonResponse {
        $tableCategory = TableCategory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return ApiResponse::created($tableCategory, 'Table category created successfully');
    }

    function update(UpdateTableCategoryRequest $request, TableCategory $tableCategory) : JsonResponse {
        $tableCategory->update([
            'name' => $request->name,
            'description' => $request->description,
         ]);

        return ApiResponse::success($tableCategory, 'Table category updated successfully');
    }

    function destroy(TableCategory $tableCategory) : JsonResponse {
        $tableCategory->delete();
        return ApiResponse::success(null, 'Table category deleted successfully');
    }
}
