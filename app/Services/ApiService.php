<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiService
{
    const DONKI_API_LIST = [
        'cme' => 'CME',
        'cme_analysis' => 'CMEAnalysis',
        'gst' => 'GST',
        'ips' => 'IPS',
        'flr' => 'FLR',
        'sep' => 'SEP',
        'mpc' => 'MPC',
        'rbe' => 'RBE',
        'hss' => 'HSS',
        'wsa_enlil_simulations' => 'WSAEnlilSimulations',
        'notifications' => 'notifications',
    ];

    protected $baseUrl;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = 'https://api.nasa.gov/DONKI';
    }

    /**
     * @param string $apiKey
     * @return array
     */
    public function getApiUrls($apiKey)
    {
        return array_map(function ($endpoint) use ($apiKey) {
            return "{$this->baseUrl}/{$endpoint}?api_key={$apiKey}";
        }, self::DONKI_API_LIST);
    }

    /**
     * @param string $fechaInicio
     * @param string $fechaTermino
     * @param string $apiKey
     * @return array
     */
    public function getApisConFechas($fechaInicio, $fechaTermino, $apiKey)
    {
        $fechaInicio = \DateTime::createFromFormat('Y-m-d', $fechaInicio);
        $fechaTermino = \DateTime::createFromFormat('Y-m-d', $fechaTermino);

        if ($fechaInicio > $fechaTermino) {
            throw new \Exception('La fecha de inicio no puede ser mayor a la fecha de término.');
        }

        $apisConFechas = [];
        foreach ($this->getApiUrls($apiKey) as $key => $url) {
            $urlConFechas = str_replace('yyyy-MM-dd', $fechaInicio->format('Y-m-d'), $url);
            $urlConFechas = str_replace('yyyy-MM-dd', $fechaTermino->format('Y-m-d'), $urlConFechas);
            $apisConFechas[$key] = $urlConFechas;
        }
        return $apisConFechas;
    }

    /**
     * @param string $fechaInicio
     * @param string $fechaTermino
     * @param string $apiKey
     * @return array
     */
    public function getInstruments($fechaInicio, $fechaTermino, $apiKey)
    {
        $apisConFechas = $this->getApisConFechas($fechaInicio, $fechaTermino, $apiKey);
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
    private function extractInstruments($data)
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
     * @param string $fechaInicio
     * @param string $fechaTermino
     * @param string $apiKey
     * @return array
     */
    public function getActivityIds($fechaInicio, $fechaTermino, $apiKey)
    {
        $apisConFechas = $this->getApisConFechas($fechaInicio, $fechaTermino, $apiKey);
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
    private function extractActivityIds($data)
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

    /**
     * @param string $fechaInicio
     * @param string $fechaTermino
     * @param string $apiKey
     * @return array
     */
    public function getInstrumentsUsage($fechaInicio, $fechaTermino, $apiKey)
    {
        $instruments = $this->getInstruments($fechaInicio, $fechaTermino, $apiKey);
        return $this->getUsagePercentages($instruments);
    }

    /**
     * @param array $instruments
     * @return array
     */
    private function getUsagePercentages($instruments)
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
}
