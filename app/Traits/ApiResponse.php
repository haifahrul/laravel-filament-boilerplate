<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    public function success($data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        // Jika data adalah array dengan key 'data' dan 'meta' dari pagination
        if (is_array($data) && array_key_exists('data', $data) && array_key_exists('meta', $data)) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data'    => $data['data'],
                'meta'    => $data['meta'],
            ], $status);
        }

        // Untuk data biasa (tanpa pagination)
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    public function error(string $message = 'Something went wrong', int $status = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    public static function paginate(LengthAwarePaginator $paginator, callable $transformer = null): array
    {
        $items = $transformer
            ? $paginator->getCollection()->map($transformer)
            : $paginator->getCollection();

        return [
            'data' => $items->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ];
    }
}
