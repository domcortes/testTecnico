<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatesRequest;
use App\Http\Requests\GetInstrumentRequest;
use Illuminate\Http\Request;
use App\Services\ApiService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

class InstrumentController extends Controller
{
    public function __construct()
    {
        set_time_limit(60);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInstruments(DatesRequest $request, ApiService $apiService, ResponseService $responseService): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $resultados = $apiService->getInstruments($validatedData['startDate'], $validatedData['endDate']);
            return $responseService->sendResponse(['instruments' => $resultados]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $responseService->sendResponse([
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInstrumentsUsage(DatesRequest $request, ApiService $apiService, ResponseService $responseService): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $usagePercentages = $apiService->getInstrumentsUsage($validatedData['startDate'], $validatedData['endDate']);
            return $responseService->sendResponse(['instruments_use' => $usagePercentages]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $responseService->sendResponse([
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @param GetInstrumentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsageByInstrument(GetInstrumentRequest $request, ApiService $apiService, ResponseService $responseService): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $result = $apiService->getUsageByInstrumentWithCounts($validatedData['instrument'], $validatedData['startDate'], $validatedData['endDate']);
            return $responseService->sendResponse($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $responseService->sendResponse([
                'errors' => $e->errors()
            ], 422);
        }
    }
}
