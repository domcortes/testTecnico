<?php

namespace App\Services;

class ApiDonkiCallService
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

    protected string $apiKey;
    protected string $baseUrl;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = 'https://api.nasa.gov/DONKI';
        $this->apiKey = env('NASA_API_KEY');
    }

    /**
     * @return array
     */
    public function getApiUrls(): array
    {
        return array_map(function ($endpoint) {
            return "{$this->baseUrl}/{$endpoint}?api_key={$this->apiKey}";
        }, self::DONKI_API_LIST);
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getApisConFechas(string $startDate, string $endDate): array
    {
        $startDate = \DateTime::createFromFormat('Y-m-d', $startDate);
        $endDate = \DateTime::createFromFormat('Y-m-d', $endDate);

        if ($startDate > $endDate) {
            throw new \Exception('La fecha de inicio no puede ser mayor a la fecha de término.');
        }

        $apisConFechas = [];
        foreach ($this->getApiUrls() as $key => $url) {
            $urlConFechas = str_replace('yyyy-MM-dd', $startDate->format('Y-m-d'), $url);
            $urlConFechas = str_replace('yyyy-MM-dd', $endDate->format('Y-m-d'), $urlConFechas);
            $apisConFechas[$key] = $urlConFechas;
        }
        return $apisConFechas;
    }
}
