<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckAppKey extends Command
{
    protected $signature = 'check:app-key';

    protected $description = 'Verify that APP_KEY is set and valid for deployment safety';

    public function handle(): int
    {
        $appKey = config('app.key');

        if (blank($appKey)) {
            $this->error('ERROR: APP_KEY is not set in .env file');
            return self::FAILURE;
        }

        if (!str_starts_with($appKey, 'base64:')) {
            $this->error('ERROR: APP_KEY does not start with "base64:" prefix');
            return self::FAILURE;
        }

        $keyLength = strlen(base64_decode(substr($appKey, 7)));

        if ($keyLength !== 32) {
            $this->error('ERROR: APP_KEY has invalid length (expected 32 bytes, got ' . $keyLength . ')');
            return self::FAILURE;
        }

        $this->info('✓ APP_KEY is valid and properly configured');
        return self::SUCCESS;
    }
}
