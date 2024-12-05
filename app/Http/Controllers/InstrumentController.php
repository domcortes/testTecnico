<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatesRequest;
use App\Http\Requests\GetInstrumentRequest;
use App\Services\ApiInstrumentService;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

class InstrumentController extends Controller
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
    public function getInstruments(DatesRequest $request, ApiInstrumentService $apiInstrumentService, ResponseService $responseService): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $resultados = $apiInstrumentService->getInstruments($validatedData['startDate'], $validatedData['endDate']);
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
    public function getInstrumentsUsage(DatesRequest $request, ApiInstrumentService $apiInstrumentService, ResponseService $responseService): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $usagePercentages = $apiInstrumentService->getInstrumentsUsage($validatedData['startDate'], $validatedData['endDate']);
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
    public function getUsageByInstrument(GetInstrumentRequest $request, ApiInstrumentService $apiInstrumentService, ResponseService $responseService): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $result = $apiInstrumentService->getUsageByInstrumentWithCounts($validatedData['instrument'], $validatedData['startDate'], $validatedData['endDate']);
            return $responseService->sendResponse($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $responseService->sendResponse([
                'errors' => $e->errors()
            ], 422);
        }
    }
}
