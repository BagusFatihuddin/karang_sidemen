<?php

namespace App\Observers;

use App\Models\DestinationImage;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;

class DestinationImageObserver
{
    public function deleting(DestinationImage $image): void
    {
        if (empty($image->cloudinary_public_id)) {
            return;
        }

        try {
            app(CloudinaryService::class)->delete(
                $image->cloudinary_public_id
            );
        } catch (\Throwable $e) {
            Log::warning(
                'Destination image delete failed',
                [
                    'destination_id' => $image->destination_id,
                    'public_id' => $image->cloudinary_public_id,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
}