<?php

namespace App\Providers;

use App\Models\DestinationImage;
use App\Observers\DestinationImageObserver;
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
    }
}