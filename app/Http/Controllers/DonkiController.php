<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class DonkiController extends Controller
{
    private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
        set_time_limit(60);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function instruments(Request $request)
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
    public function activityIds(Request $request)
    {
        $fechaInicio = $request->input('startDate');
        $fechaTermino = $request->input('endDate');
        $apiKey = env('NASA_API_KEY');

        try {
            $resultados = $this->apiService->getActivityIds($fechaInicio, $fechaTermino, $apiKey);
            return response()->json(['activityIDs' => $resultados]);
        } catch (\Exception $e) {
            Log::error('Error en activityIds: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener IDs de actividad.'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInstrumentsUsage(Request $request)
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
}
