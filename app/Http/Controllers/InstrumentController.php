<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetInstrumentRequest;
use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class InstrumentController extends Controller
{
    private ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
        set_time_limit(60);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInstruments(Request $request): \Illuminate\Http\JsonResponse
    {
        $fechaInicio = $request->input('startDate');
        $fechaTermino = $request->input('endDate');
        $apiKey = env('NASA_API_KEY');

        try {
            $resultados = $this->apiService->getInstruments($fechaInicio, $fechaTermino, $apiKey);
            return response()->json(['instruments' => $resultados]);
        } catch (\Exception $e) {
            Log::error('Error en instruments: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener instrumentos.'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInstrumentsUsage(Request $request): \Illuminate\Http\JsonResponse
    {
        $fechaInicio = $request->input('startDate');
        $fechaTermino = $request->input('endDate');
        $apiKey = env('NASA_API_KEY');

        try {
            $usagePercentages = $this->apiService->getInstrumentsUsage($fechaInicio, $fechaTermino, $apiKey);
            return response()->json(['instruments_use' => $usagePercentages]);
        } catch (\Exception $e) {
            Log::error('Error en getInstrumentsUsage: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener el uso de instrumentos.'], 500);
        }
    }

    /**
     * @param GetInstrumentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsageByInstrument(GetInstrumentRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $fechaInicio = $request->input('startDate');
            $fechaTermino = $request->input('endDate');
            $apiKey = env('NASA_API_KEY');

            $usageByInstrument = $this->apiService->getUsageByInstrument($validatedData['instrument'], $fechaInicio, $fechaTermino, $apiKey);

            $instrumentCounts = [];

            foreach ($usageByInstrument as $usage) {
                $key = $usage['instrument'] . '|' . $usage['activityId'];

                if (!isset($instrumentCounts[$key])) {
                    $instrumentCounts[$key] = 0;
                }

                $instrumentCounts[$key]++;
            }

            $result = [];
            foreach ($instrumentCounts as $key => $count) {
                list($instrument, $activityId) = explode('|', $key);
                $result[] = [
                    'instrument' => $instrument,
                    'activityId' => $activityId,
                    'count' => $count,
                ];
            }

            $totalActivities = array_sum(array_column($result, 'count'));

            $filteredResult = array_filter($result, function ($item) use ($validatedData) {
                return $item['instrument'] == $validatedData['instrument'];
            });

            $filteredTotal = array_sum(array_column($filteredResult, 'count'));
            $percentage = ($filteredTotal / $totalActivities) * 100;

            return response()->json([
                'instrument_activity' => [
                    $validatedData['instrument'] => [
                        $filteredResult[0]['activityId'] => round($percentage, 2)
                    ]
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }
}
