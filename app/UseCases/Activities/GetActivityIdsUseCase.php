<?php

namespace App\UseCases\Activities;

use App\Services\ApiActivityService;

class GetActivityIdsUseCase
{
    protected ApiActivityService $apiActivityService;

    public function __construct(ApiActivityService $apiActivityService)
    {
        $this->apiActivityService = $apiActivityService;
    }

    public function execute(string $startDate, string $endDate): array
    {
        return $this->apiActivityService->getActivityIds($startDate, $endDate);
    }
}
