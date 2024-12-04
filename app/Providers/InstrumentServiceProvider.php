<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ApiService;

class InstrumentServiceProvider extends ServiceProvider
{
    /**
     * Registra los servicios en el contenedor de servicios.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ApiService::class, function ($app) {
            return new ApiService();
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
