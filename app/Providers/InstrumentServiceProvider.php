<?php

namespace App\Providers;

use App\Services\ApiActivityService;
use App\Services\ApiDonkiCallService;
use App\Services\ApiInstrumentService;
use Illuminate\Support\ServiceProvider;


class InstrumentServiceProvider extends ServiceProvider
{
    /**
     * Registra los servicios en el contenedor de servicios.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ApiInstrumentService::class, function ($app) {
            return new ApiInstrumentService($app->make(ApiDonkiCallService::class));
        });

        $this->app->singleton(ApiActivityService::class, function ($app) {
            return new ApiActivityService($app->make(ApiDonkiCallService::class));
        });
    }

    /**
     * Realiza cualquier configuración adicional después de que todos los servicios hayan sido registrados.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
