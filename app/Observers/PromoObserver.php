<?php

namespace App\Observers;

use App\Models\Promo;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;

class PromoObserver
{
    public function deleting(Promo $promo): void
    {
        if (empty($promo->image_public_id)) {
            return;
        }

        try {
            app(CloudinaryService::class)->delete(
                $promo->image_public_id
            );
        } catch (\Throwable $e) {
            Log::warning(
                'Promo image delete failed',
                [
                    'promo_id' => $promo->id,
                    'public_id' => $promo->image_public_id,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
}
