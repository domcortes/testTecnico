<?php

namespace App\Services;

class ResponseService
{
    /**
     * @param array $dataToResponse
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse(array $dataToResponse, int $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json($dataToResponse, $statusCode);
    }
}
