<?php

namespace App\UseCases\Instruments;

use App\Services\ApiInstrumentService;

class GetInstrumentUsageUseCase
{
    protected ApiInstrumentService $apiInstrumentService;

    public function __construct(ApiInstrumentService $apiInstrumentService)
    {
        $this->apiInstrumentService = $apiInstrumentService;
    }

    public function execute(string $startDate, string $endDate): array
    {
        return $this->apiInstrumentService->getInstrumentsUsage($startDate, $endDate);
    }
}
