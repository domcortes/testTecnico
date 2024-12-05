<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiActivityService
{
    private $apiDonkiCallService;

    /**
     * @param ApiDonkiCallService $apiDonkiCallService
     */
    public function __construct(ApiDonkiCallService $apiDonkiCallService)
    {
        $this->apiDonkiCallService = $apiDonkiCallService;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getActivityIds(string $startDate, string $endDate): array
    {
        $apisConFechas = $this->apiDonkiCallService->getApisConFechas($startDate, $endDate);
        $resultados = [];

        foreach ($apisConFechas as $url) {
            $respuesta = Http::get($url);
            if ($respuesta->failed()) {
                Log::error('Error al obtener datos de la API: ' . $respuesta->body());
                throw new \Exception('Error al obtener datos de la API: ' . $respuesta->body());
            }

            $data = $respuesta->json();
            $activityIds = $this->extractActivityIds($data);
            $resultados = array_merge($resultados, $activityIds);
        }

        return $resultados;
    }

    /**
     * @param array $data
     * @return array
     */
    private function extractActivityIds(array $data): array
    {
        $activityIds = [];
        $activityIdsFromData = data_get($data, '*.activityID');

        if ($activityIdsFromData) {
            foreach ($activityIdsFromData as $activityId) {
                if (is_string($activityId)) {
                    $activityId = explode('-', $activityId);
                    $activityIds[] = $activityId[3] . '-' . $activityId[4];
                }
            }
        }

        return collect($activityIds)->unique()->values()->all();
    }
}
