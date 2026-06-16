<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class CacheVersion
{
    public static function bump(string $key): void
    {
        Cache::forever($key, ((int) Cache::get($key, 1)) + 1);
    }
}
