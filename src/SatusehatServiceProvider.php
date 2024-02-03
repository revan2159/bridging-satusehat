<?php

namespace Rsudipodev\BridgingSatusehat;

use Illuminate\Support\ServiceProvider;

class SatusehatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '../config/satusehat.php' => config_path('satusehat.php'),
        ], 'config');
    }
}
