<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * استجابة نجاح عامة
     */
   public function successResponse($data = null, string $message = 'تم بنجاح', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    /**
     * استجابة فشل عامة
     */
     
   public function errorResponse(string $message = 'حدث خطأ ما', int $code = 500, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ], $code);
    }

    /**
     * استجابة نجاح مع بيانات مجمعة (ResourceCollection)
     */
    public function paginatedResponse(ResourceCollection $collection, string $message = 'تم بنجاح', int $code = 200): JsonResponse
    {
        return response()->json([
            'success'     => true,
            'message'     => $message,
            'data'        => $collection->items(),
            'pagination'  => [
                'total'        => $collection->total(),
                'count'        => $collection->count(),
                'per_page'     => $collection->perPage(),
                'current_page' => $collection->currentPage(),
                'last_page'    => $collection->lastPage(),
            ]
        ], $code);
    }
}
