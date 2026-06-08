<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Get public settings (whitelisted only).
     */
    public function publicSettings(): JsonResponse
    {
        $cacheKey = 'settings:public:whitelist';
        $whitelist = ['village_name', 'tagline', 'global_whatsapp', 'social_links', 'maps_url'];

        $settings = Cache::remember($cacheKey, 60 * 60, function () use ($whitelist) {
            return Setting::whereIn('key', $whitelist)
                ->get()
                ->mapWithKeys(fn($setting) => [$setting->key => $setting->value])
                ->toArray();
        });

        return response()->json($settings);
    }
}
