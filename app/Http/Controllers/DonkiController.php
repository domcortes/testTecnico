<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class DonkiController extends Controller
{
    const DONKI_API_LIST = [
        'cme' => 'https://api.nasa.gov/DONKI/CME?startDate=yyyy-MM-dd&endDate=yyyy-MM-dd&api_key=DEMO_KEY',
        'cme_analysis' => 'https://api.nasa.gov/DONKI/CMEAnalysis?startDate=yyyy-MM-dd&endDate=yyyy-MM-dd&mostAccurateOnly=true&speed=500&halfAngle=30&catalog=ALL&api_key=DEMO_KEY',
        'gst' => 'https://api.nasa.gov/DONKI/GST?startDate=yyyy-MM-dd&endDate=yyyy-MM-dd&api_key=DEMO_KEY',
        'ips' => 'https://api.nasa.gov/DONKI/IPS?startDate=yyyy-MM-dd&endDate=yyyy-MM-dd&location=LOCATION&catalog=CATALOG&api_key=DEMO_KEY',
        'flr' => 'https://api.nasa.gov/DONKI/FLR?startDate=yyyy-MM-dd&endDate=yyyy-MM-dd&api_key=DEMO_KEY',
        'sep' => 'https://api.nasa.gov/DONKI/SEP?startDate=yyyy-MM-dd&endDate=yyyy-MM-dd&api_key=DEMO_KEY',
        'mpc' => 'https://api.nasa.gov/DONKI/MPC?startDate=yyyy-MM-dd&endDate=yyyy-MM-dd&api_key=DEMO_KEY',
        'rbe' => 'https://api.nasa.gov/DONKI/RBE?startDate=yyyy-MM-dd&endDate=yyyy-MM-dd&api_key=DEMO_KEY',
        'hss' => 'https://api.nasa.gov/DONKI/HSS?startDate=yyyy-MM-dd&endDate=yyyy-MM-dd&api_key=DEMO_KEY',
        'wsa_enlil_simulations' => 'https://api.nasa.gov/DONKI/WSAEnlilSimulations?startDate=yyyy-MM-dd&endDate=yyyy-MM-dd&api_key=DEMO_KEY',
        'notifications' => 'https://api.nasa.gov/DONKI/notifications?startDate=yyyy-MM-dd&endDate=yyyy-MM-dd&type=all&api_key=DEMO_KEY',
    ];

    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $fechaInicio
     * @param string $fechaTermino
     * @return array
     */
    private function getApisConFechas($fechaInicio, $fechaTermino)
    {
        $fechaInicio = \DateTime::createFromFormat('Y-m-d', $fechaInicio);
        $fechaTermino = \DateTime::createFromFormat('Y-m-d', $fechaTermino);

        if ($fechaInicio > $fechaTermino) {
            throw new \Exception('La fecha de inicio no puede ser mayor a la fecha de término.');
        }

        $apisConFechas = [];
        foreach (self::DONKI_API_LIST as $key => $url) {
            $urlConFechas = str_replace('yyyy-MM-dd', $fechaInicio->format('Y-m-d'), $url);
            $urlConFechas = str_replace('yyyy-MM-dd', $fechaTermino->format('Y-m-d'), $urlConFechas);
            $urlConFechas = str_replace('DEMO_KEY', env('NASA_API_KEY'), $urlConFechas);
            $apisConFechas[$key] = $urlConFechas;
        }
        return $apisConFechas;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function instruments(Request $request)
    {
        $fechaInicio = $request->input('startDate');
        $fechaTermino = $request->input('endDate');

        $apisConFechas = $this->getApisConFechas($fechaInicio, $fechaTermino);
        $resultados = $this->getInstruments($apisConFechas);

        return response()->json([
            'instruments' => $resultados
        ]);
    }

    /**
     * @param array $apisConFechas
     * @return array
     */
    private function getInstruments($apisConFechas)
    {
        $resultados = [];
        foreach ($apisConFechas as $key => $url) {
            $respuesta = $this->client->request('GET', $url)->getBody()->getContents();
            $data = json_decode($respuesta, true);

            $instruments = $this->extractInstruments($data);
            $resultados = array_merge($resultados, $instruments);
        }

        return $resultados;
    }

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

        $instruments = collect($instruments)->unique('displayName')->pluck('displayName')->values()->all();

        return $instruments;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activityIds(Request $request)
    {
        $fechaInicio = $request->input('startDate');
        $fechaTermino = $request->input('endDate');

        $apisConFechas = $this->getApisConFechas($fechaInicio, $fechaTermino);
        $resultados = $this->getActivityIds($apisConFechas);

        return response()->json([
            'activitIDs' => $resultados
        ]);
    }

    /**
     * @param array $apisConFechas
     * @return array
     */
    private function getActivityIds($apisConFechas)
    {
        $resultados = [];
        foreach ($apisConFechas as $key => $url) {
            $respuesta = $this->client->request('GET', $url)->getBody()->getContents();
            $data = json_decode($respuesta, true);

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

        $activityIds = collect($activityIds)->unique()->values()->all();

        return $activityIds;
    }

}
