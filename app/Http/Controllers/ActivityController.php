<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatesRequest;
use App\Services\ApiActivityService;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        set_time_limit(60);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActivityIds(DatesRequest $request, ApiActivityService $apiActivityService, ResponseService $responseService): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validated();
        $fechaInicio = $validatedData['startDate'];
        $fechaTermino = $validatedData['endDate'];

        try {
            $resultados = $apiActivityService->getActivityIds($fechaInicio, $fechaTermino);
            return $responseService->sendResponse(['activityIDs' => $resultados]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $responseService->sendResponse([
                'errors' => $e->errors()
            ], 422);
        }
    }
}
