<?php

namespace App\Observers;

use App\Models\TripPackage;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;

class TripPackageObserver
{
    public function deleting(TripPackage $tripPackage): void
    {
        if (empty($tripPackage->image_public_id)) {
            return;
        }

        try {
            app(CloudinaryService::class)->delete(
                $tripPackage->image_public_id
            );
        } catch (\Throwable $e) {
            Log::warning(
                'Trip package image delete failed',
                [
                    'trip_package_id' => $tripPackage->id,
                    'public_id' => $tripPackage->image_public_id,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
}
