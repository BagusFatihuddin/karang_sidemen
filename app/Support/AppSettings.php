<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class AppSettings
{
    private const CACHE_KEY = 'settings:all';

    /**
     * Get setting value.
     */
    public static function get(
        string $key,
        mixed $default = null
    ): mixed {
        return self::all()[$key] ?? $default;
    }

    /**
     * Get all settings as key-value pairs.
     *
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return Cache::remember(
            self::CACHE_KEY,
            now()->addHour(),
            fn (): array => Setting::query()
                ->pluck('value', 'key')
                ->all()
        );
    }

    /**
     * Set setting value.
     */
    public static function set(
        string $key,
        mixed $value
    ): void {
        Setting::query()->updateOrCreate(
            [
                'key' => $key,
            ],
            [
                'value' => $value,
                'updated_at' => now(),
            ]
        );

        self::clearCache();
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget('settings:public:whitelist');
    }
}
