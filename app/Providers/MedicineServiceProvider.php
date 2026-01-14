<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MedicineService;

class MedicineServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind service ke container
        $this->app->singleton(MedicineService::class, function ($app) {
            return new MedicineService(
                config('services.rsds.email'),
                config('services.rsds.password'),
                config('services.rsds.base_url')
            );
        });
    }
}
