<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($data, $message = 'Success', $status = 200)
    {
        if ($data instanceof \Illuminate\Http\Resources\Json\ResourceCollection) {
            $response = $data->response()->getData(true);

            $response['meta']['status'] = $status;
            $response['meta']['message'] = $message;

            return response()->json($response, $status);
        }

        return response()->json([
            'data' => $data,
            'meta' => [
                'status' => $status,
                'message' => $message,
            ]
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
