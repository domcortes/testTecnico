<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActivityController extends Controller
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
    public function getActivityIds(Request $request)
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
}
