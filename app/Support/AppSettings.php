<?php

namespace App\Support;

use App\Models\Setting;

class AppSettings
{
    /**
     * Get setting value.
     */
    public static function get(
        string $key,
        mixed $default = null
    ): mixed {
        return Setting::query()
            ->where('key', $key)
            ->value('value')
            ?? $default;
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
    }
}