<?php

namespace App\UseCases\Instruments;

use App\Services\ApiInstrumentService;

class GetInstrumentsUseCase
{
    protected ApiInstrumentService $apiInstrumentService;

    public function __construct(ApiInstrumentService $apiInstrumentService)
    {
        $this->apiInstrumentService = $apiInstrumentService;
    }

    public function execute(string $startDate, string $endDate): array
    {
        return $this->apiInstrumentService->getInstruments($startDate, $endDate);
    }
}
