<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

final class ApiResponse
{
    /**
     * Success response: { success: true, message: '...', data: {...} }
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Error response: { success: false, message: '...', errors: {...} }
     */
    public static function error(
        string $message = 'An error occurred',
        mixed $errors = null,
        int $status = 400,
    ): JsonResponse {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    /**
     * Paginated response: { success: true, data: [...], meta: { current_page, ... } }
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        string $message = 'Success',
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    /**
     * Created response (201)
     */
    public static function created(
        mixed $data = null,
        string $message = 'Created successfully',
    ): JsonResponse {
        return self::success($data, $message, 201);
    }

    /**
     * No content response (204)
     */
    public static function noContent(): Response
    {
        return response()->noContent();
    }

    /**
     * Unauthorized response (401)
     */
    public static function unauthorized(string $message = 'Unauthenticated'): JsonResponse
    {
        return self::error($message, null, 401);
    }

    /**
     * Forbidden response (403)
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::error($message, null, 403);
    }

    /**
     * Not found response (404)
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, null, 404);
    }

    /**
     * Validation error response (422)
     */
    public static function validationError(
        mixed $errors,
        string $message = 'Validation failed',
    ): JsonResponse {
        return self::error($message, $errors, 422);
    }
}
