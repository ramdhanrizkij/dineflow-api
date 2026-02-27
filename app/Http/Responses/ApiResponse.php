<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Return a generic success response.
     */
    public static function success(mixed $data, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Return a 201 Created response.
     */
    public static function created(mixed $data, string $message = 'Created successfully'): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], 201);
    }

    /**
     * Return a paginated response with a standard meta block.
     *
     * @param  LengthAwarePaginator  $paginator
     * @param  string                $message
     * @param  array                 $extraMeta   Any additional key-value pairs to merge into meta (e.g. sort info)
     */
    public static function paginated(LengthAwarePaginator $paginator, string $message = 'Data retrieved successfully', array $extraMeta = []): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $paginator->items(),
            'meta'    => array_merge([
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ], $extraMeta),
        ]);
    }

    /**
     * Return a success response for delete operations (no data body).
     */
    public static function deleted(string $message = 'Deleted successfully'): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
        ]);
    }

    /**
     * Return an error response.
     */
    public static function error(string $message = 'An error occurred', int $status = 400): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
        ], $status);
    }
}
