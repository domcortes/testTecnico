<?php

namespace App\Providers;

use App\Services\ApiActivityService;
use App\Services\ApiDonkiCallService;
use App\Services\ApiInstrumentService;
use Illuminate\Support\ServiceProvider;


class InstrumentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ApiInstrumentService::class, function ($app) {
            return new ApiInstrumentService($app->make(ApiDonkiCallService::class));
        });

        $this->app->singleton(ApiActivityService::class, function ($app) {
            return new ApiActivityService($app->make(ApiDonkiCallService::class));
        });
    }

    public function boot()
    {
        //
    }
}
