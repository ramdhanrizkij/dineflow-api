<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(): JsonResponse
    {
        $tables = Table::select('id', 'code', 'capacity', 'status', 'created_at', 'updated_at')
            ->orderBy('code', 'asc')
            ->get();

        return ApiResponse::success($tables, 'Tables retrieved successfully');
    }

    public function paginate(Request $request): JsonResponse
    {
        $perPage   = (int) $request->query('per_page', 10);
        $search    = $request->query('search');
        $status    = $request->query('status');
        $sort      = $request->query('sort', 'created_at');
        $direction = $request->query('sort_direction', 'asc');

        $allowedSorts = ['id', 'code', 'capacity', 'status', 'created_at'];
        $sort         = in_array($sort, $allowedSorts) ? $sort : 'created_at';
        $direction    = in_array(strtolower($direction), ['asc', 'desc']) ? strtolower($direction) : 'asc';

        $query = Table::select('id', 'code', 'capacity', 'status', 'created_at', 'updated_at');

        if ($search) {
            $query->where('code', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tables = $query->orderBy($sort, $direction)->paginate($perPage);

        return ApiResponse::paginated($tables, 'Tables retrieved successfully', [
            'sort'           => $sort,
            'sort_direction' => $direction,
        ]);
    }

    public function store(StoreTableRequest $request): JsonResponse
    {
        $table = Table::create([
            'code'     => $request->code,
            'capacity' => $request->capacity,
            'status'   => $request->status,
        ]);

        return ApiResponse::created([
            'id'         => $table->id,
            'code'       => $table->code,
            'capacity'   => $table->capacity,
            'status'     => $table->status,
            'created_at' => $table->created_at,
            'updated_at' => $table->updated_at,
        ], 'Table created successfully');
    }

    public function show(Table $table): JsonResponse
    {
        return ApiResponse::success([
            'id'         => $table->id,
            'code'       => $table->code,
            'capacity'   => $table->capacity,
            'status'     => $table->status,
            'created_at' => $table->created_at,
            'updated_at' => $table->updated_at,
        ], 'Table retrieved successfully');
    }

    public function update(UpdateTableRequest $request, Table $table): JsonResponse
    {
        $table->update([
            'code'     => $request->code,
            'capacity' => $request->capacity,
            'status'   => $request->status,
        ]);

        return ApiResponse::success([
            'id'         => $table->id,
            'code'       => $table->code,
            'capacity'   => $table->capacity,
            'status'     => $table->status,
            'created_at' => $table->created_at,
            'updated_at' => $table->updated_at,
        ], 'Table updated successfully');
    }

    public function destroy(Table $table): JsonResponse
    {
        $table->delete();

        return ApiResponse::deleted('Table deleted successfully');
    }
}
