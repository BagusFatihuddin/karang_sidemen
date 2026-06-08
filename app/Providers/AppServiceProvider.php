<?php

namespace App\Providers;

use App\Models\DestinationImage;
use App\Models\Promo;
use App\Models\TripPackage;
use App\Observers\DestinationImageObserver;
use App\Observers\PromoObserver;
use App\Observers\TripPackageObserver;
use Illuminate\Support\ServiceProvider;

/**
 * DEPLOYMENT CHECKLIST (Shared Hosting)
 * 
 * After deployment, run these commands:
 * 1. php artisan migrate           (run pending migrations)
 * 2. php artisan optimize:clear    (clear optimization cache)
 * 3. php artisan config:cache      (cache config for performance)
 * 4. php artisan route:cache       (cache routes for performance)
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        DestinationImage::observe(
            DestinationImageObserver::class
        );

        Promo::observe(
            PromoObserver::class
        );

        TripPackage::observe(
            TripPackageObserver::class
        );
    }
}

