<?php

namespace App\Providers;

use App\Models\DestinationImage;
use App\Models\Promo;
use App\Observers\DestinationImageObserver;
use App\Observers\PromoObserver;
use Illuminate\Support\ServiceProvider;

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
    }
}
