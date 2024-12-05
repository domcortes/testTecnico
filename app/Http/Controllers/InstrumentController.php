<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatesRequest;
use App\Http\Requests\GetInstrumentRequest;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use App\UseCases\Instruments\GetInstrumentsUseCase;
use App\UseCases\Instruments\GetInstrumentUsageUseCase;
use App\UseCases\Instruments\GetUsageByInstrumentUseCase;

class InstrumentController extends Controller
{
    protected GetInstrumentsUseCase $getInstrumentsUseCase;
    protected GetInstrumentUsageUseCase $getInstrumentUsageUseCase;
    protected GetUsageByInstrumentUseCase $getUsageByInstrumentUseCase;
    protected ResponseService $responseService;

    public function __construct(
        GetInstrumentsUseCase $getInstrumentsUseCase,
        GetInstrumentUsageUseCase $getInstrumentUsageUseCase,
        GetUsageByInstrumentUseCase $getUsageByInstrumentUseCase,
        ResponseService $responseService
    ) {
        $this->getInstrumentsUseCase = $getInstrumentsUseCase;
        $this->getInstrumentUsageUseCase = $getInstrumentUsageUseCase;
        $this->getUsageByInstrumentUseCase = $getUsageByInstrumentUseCase;
        $this->responseService = $responseService;
        set_time_limit(60);
    }

    public function getInstruments(DatesRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $resultados = $this->getInstrumentsUseCase->execute($validatedData['startDate'], $validatedData['endDate']);
            return $this->responseService->sendResponse(['instruments' => $resultados]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseService->sendResponse([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function getInstrumentsUsage(DatesRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $usagePercentages = $this->getInstrumentUsageUseCase->execute($validatedData['startDate'], $validatedData['endDate']);
            return $this->responseService->sendResponse(['instruments_use' => $usagePercentages]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseService->sendResponse([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function getUsageByInstrument(GetInstrumentRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $result = $this->getUsageByInstrumentUseCase->execute($validatedData['instrument'], $validatedData['startDate'], $validatedData['endDate']);
            return $this->responseService->sendResponse($result);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseService->sendResponse([
                'errors' => $e->errors()
            ], 422);
        }
    }
}
