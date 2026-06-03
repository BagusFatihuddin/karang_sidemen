<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seed settings.
     */
    public function run(): void
    {
        $keys = [
            'village_name',
            'tagline',
            'global_whatsapp',
            'cloudinary_api_key',
            'cloudinary_api_secret',
            'cloudinary_cloud_name',
            'social_instagram',
            'social_facebook',
            'social_tiktok',
            'google_maps_embed_url',
        ];

        foreach ($keys as $key) {
            Setting::query()->updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'value' => '',
                    'updated_at' => now(),
                ]
            );
        }
    }
}