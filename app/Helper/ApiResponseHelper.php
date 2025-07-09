<?php

namespace App\Helper;

use Illuminate\Database\Eloquent\Collection;

class ApiResponseHelper
{
    public static function success($data, string $message = 'Data retrieved successfully', int $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public static function error(string $message = 'An error occurred', int $statusCode = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $statusCode);
    }

    public static function paginated($outputData, string $message = 'Data retrieved successfully')
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $outputData->items(),
            'meta' => [
                'per_page' => $outputData->perPage(),
                'total' => $outputData->total(),
                'current_page' => $outputData->currentPage(),
                'first_page' => $outputData->firstItem(),
                'last_page' => $outputData->lastPage(),
                'from' => $outputData->firstItem(),
                'to' => $outputData->lastItem(),
                'next_page_url' => $outputData->nextPageUrl(),
                'prev_page_url' => $outputData->previousPageUrl(),
            ]
        ]);
    }
}
