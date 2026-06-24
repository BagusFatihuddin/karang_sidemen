<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\AppSettings;
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
        $whitelist = [
            'village_name',
            'tagline',
            'global_whatsapp',
            'brand_mark_text',
            'brand_logo_url',
            'brand_logo_alt',
            'social_instagram',
            'social_facebook',
            'social_tiktok',
            'google_maps_embed_url',
            'homepage_hero_eyebrow',
            'homepage_hero_title_line_1',
            'homepage_hero_title_line_2',
            'homepage_hero_cta_label',
            'media_homepage_hero_image_url',
            'homepage_reel_eyebrow',
            'homepage_reel_title',
            'homepage_portal_eyebrow',
            'homepage_portal_title',
            'homepage_portal_body',
            'homepage_zoom_items',
            'homepage_breathing_eyebrow',
            'homepage_breathing_title',
            'homepage_breathing_body',
            'media_homepage_breathing_image_url',
            'homepage_horizontal_eyebrow',
            'homepage_horizontal_title',
            'homepage_horizontal_hint',
            'homepage_horizontal_items',
            'homepage_experience_eyebrow',
            'homepage_experience_title',
            'homepage_highlight_eyebrow',
            'homepage_highlight_title',
            'homepage_reviews_eyebrow',
            'homepage_reviews_title',
            'homepage_final_eyebrow',
            'homepage_final_title',
            'homepage_final_cta_label',
            'media_homepage_final_image_url',
            'media_footer_cta_image_url',
            'media_destinations_hero_image_url',
            'media_about_hero_fallback_image_url',
            'media_about_story_image_url',
            'media_about_organization_chart_image_url',
            'about_organization_title',
            'media_reviews_hero_image_url',
            'media_packages_hero_fallback_image_url',
            'media_packages_empty_image_url',
            'media_package_card_fallback_1_url',
            'media_package_card_fallback_2_url',
            'media_package_card_fallback_3_url',
            'media_guides_hero_fallback_image_url',
            'media_guides_empty_image_url',
            'media_guides_note_image_url',
        ];

        $settings = Cache::remember($cacheKey, 60 * 60, function () use ($whitelist) {
            $allSettings = AppSettings::all();

            return collect($whitelist)
                ->filter(fn (string $key): bool => array_key_exists($key, $allSettings))
                ->mapWithKeys(fn (string $key): array => [$key => $allSettings[$key]])
                ->toArray();
        });

        return response()->json($settings);
    }
}
