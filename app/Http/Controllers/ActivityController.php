<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatesRequest;
use App\Services\ResponseService;
use App\UseCases\Activities\GetActivityIdsUseCase;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    protected GetActivityIdsUseCase $getActivityIdsUseCase;
    protected ResponseService $responseService;

    public function __construct(GetActivityIdsUseCase $getActivityIdsUseCase, ResponseService $responseService)
    {
        $this->getActivityIdsUseCase = $getActivityIdsUseCase;
        $this->responseService = $responseService;
        set_time_limit(60);
    }

    public function getActivityIds(DatesRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $fechaInicio = $validatedData['startDate'];
        $fechaTermino = $validatedData['endDate'];

        try {
            $resultados = $this->getActivityIdsUseCase->execute($fechaInicio, $fechaTermino);
            return $this->responseService->sendResponse(['activityIDs' => $resultados]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseService->sendResponse([
                'errors' => $e->errors()
            ], 422);
        }
    }
}
