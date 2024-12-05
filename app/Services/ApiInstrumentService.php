<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiInstrumentService
{
    protected ApiDonkiCallService $apiDonkiCallService;

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
    public function getInstruments(string $startDate, string $endDate): array
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
            $instruments = $this->extractInstruments($data);
            $resultados = array_merge($resultados, $instruments);
        }

        return $resultados;
    }

    /**
     * @param array $data
     * @return array
     */
    private function extractInstruments(array $data): array
    {
        $instruments = [];
        $instrumentsFromData = data_get($data, '*.instruments');

        if ($instrumentsFromData) {
            foreach ($instrumentsFromData as $instrumentGroup) {
                if (is_array($instrumentGroup)) {
                    $instruments = array_merge($instruments, $instrumentGroup);
                }
            }
        }

        return collect($instruments)->unique('displayName')->pluck('displayName')->values()->all();
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getInstrumentsUsage(string $startDate, string $endDate): array
    {
        $instruments = $this->getInstruments($startDate, $endDate);
        return $this->getUsagePercentages($instruments);
    }

    /**
     * @param array $instruments
     * @return array
     */
    private function getUsagePercentages(array $instruments): array
    {
        $instrumentCount = [];
        
        foreach ($instruments as $instrument) {
            if (!empty($instrument)) {
                if (!isset($instrumentCount[$instrument])) {
                    $instrumentCount[$instrument] = 0;
                }
                $instrumentCount[$instrument]++;
            }
        }

        $totalInstruments = array_sum($instrumentCount);
        
        $usagePercentages = [];
        foreach ($instrumentCount as $name => $count) {
            $usagePercentages[$name] = round($count / $totalInstruments, 2);
        }

        return $usagePercentages;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getUsageByInstrument(string $startDate, string $endDate): array
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
            $usageByInstrument = $this->extractUsageByInstrument($data);
            $resultados = array_merge($resultados, $usageByInstrument);
        }

        return $resultados;
    }

    /**
     * @param array $data
     * @return array
     */
    private function extractUsageByInstrument(array $data): array
    {
        $instruments = [];
        $instrumentsFromData = data_get($data, '*.instruments');
        $activityIdsFromData = data_get($data, '*.activityID');

        if ($instrumentsFromData) {
            foreach ($instrumentsFromData as $index => $instrumentGroup) {
                $activityId = explode('-', $activityIdsFromData[$index]);

                if (isset($activityId[3]) && isset($activityId[4])) {
                    $activityKey = $activityId[3] . '-' . $activityId[4];

                    foreach ($instrumentGroup as $instrument) {
                        $instruments[] = [
                            'instrument' => $instrument['displayName'],
                            'activityId' => $activityKey
                        ];
                    }
                }
            }
        }

        return $instruments;
    }

    /**
     * @param string $instrument
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getUsageByInstrumentWithCounts(string $instrument, string $startDate, string $endDate): array
    {
        $usageByInstrument = $this->getUsageByInstrument($startDate, $endDate);
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
            list($currentInstrument, $activityId) = explode('|', $key);
            $result[] = [
                'instrument' => $currentInstrument,
                'activityId' => $activityId,
                'count' => $count,
            ];
        }

        $totalActivities = array_sum(array_column($result, 'count'));
        $filteredResult = array_filter($result, function ($item) use ($instrument) {
            return $item['instrument'] == $instrument;
        });

        $filteredTotal = array_sum(array_column($filteredResult, 'count'));
        $percentage = ($filteredTotal / $totalActivities) * 100;

        return [
            'instrument_activity' => [
                $instrument => [
                    $filteredResult[0]['activityId'] => round($percentage, 2)
                ]
            ]
        ];
    }
}
