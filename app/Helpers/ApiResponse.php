<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($data = null, $message = 'Success', $status = 200)
    {
        return response()->json([
            'data' => $data,
            'meta' => [
                'status' => $status,
                'message' => $message,
            ],
        ], $status);
    }

    public static function error($message = 'Something went wrong', $status = 400, $data = null)
    {
        return response()->json([
            'data' => $data,
            'meta' => [
                'status' => $status,
                'message' => $message,
            ],
        ], $status);
    }
}