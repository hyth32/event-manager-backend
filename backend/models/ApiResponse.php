<?php

namespace backend\models;

class ApiResponse
{
    public static function successResponse(string $message = 'OK', array $data = []): array
    {
        return static::formatResponse('success', $message, $data);
    }

    public static function errorResponse(string $message = 'error', array $data = []): array
    {
        return static::formatResponse('error', $message, $data);
    }

    private static function formatResponse(string $status, string $message, array $data): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
    }
}