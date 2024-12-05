<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatesRequest;
use App\Services\ApiService;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct()
    {
        set_time_limit(60);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActivityIds(DatesRequest $request, ApiService $apiService, ResponseService $responseService): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validated();
        $fechaInicio = $validatedData['startDate'];
        $fechaTermino = $validatedData['endDate'];

        try {
            $resultados = $apiService->getActivityIds($fechaInicio, $fechaTermino);
            return $responseService->sendResponse(['activityIDs' => $resultados]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $responseService->sendResponse([
                'errors' => $e->errors()
            ], 422);
        }
    }
}
