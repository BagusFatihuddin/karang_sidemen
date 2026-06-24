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
                ->map(fn (mixed $value): mixed => self::decodeValue($value))
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
                'value' => self::encodeValue($value),
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

    private static function encodeValue(mixed $value): mixed
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return $value;
    }

    private static function decodeValue(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);

        if ($trimmed === '' || ! in_array($trimmed[0], ['[', '{'], true)) {
            return $value;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }
}
