<?php

namespace App\UseCases\Instruments;

use App\Services\ApiInstrumentService;

class GetUsageByInstrumentUseCase
{
    protected ApiInstrumentService $apiInstrumentService;

    public function __construct(ApiInstrumentService $apiInstrumentService)
    {
        $this->apiInstrumentService = $apiInstrumentService;
    }

    public function execute(string $instrument, string $startDate, string $endDate): array
    {
        return $this->apiInstrumentService->getUsageByInstrumentWithCounts($instrument, $startDate, $endDate);
    }
}
