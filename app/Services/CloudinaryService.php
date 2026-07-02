<?php

namespace App\Services;

use App\Support\AppSettings;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class CloudinaryService
{
    public function upload(
        UploadedFile $file,
        string $folder
    ): array {
        if (! $this->isConfigured()) {
            Log::warning(
                'Cloudinary upload skipped: credentials not configured.'
            );

            throw new RuntimeException(
                'Cloudinary belum dikonfigurasi.'
            );
        }

        try {
            $this->configure();

            $result = (new UploadApi())->upload(
                $file->getRealPath(),
                [
                    'folder' => trim($folder, '/'),

                    // optimization default
                    'quality' => 80,
                    'fetch_format' => 'auto',
                ]
            );

            return [
                'url' => $result['secure_url'] ?? '',
                'public_id' => $result['public_id'] ?? '',
            ];
        } catch (Throwable $e) {
            Log::error(
                'Cloudinary upload failed',
                [
                    'message' => $e->getMessage(),
                ]
            );

            throw new RuntimeException(
                'Gagal upload gambar ke Cloudinary.'
            );
        }
    }

    public function delete(
        string $publicId
    ): bool {
        if (! $this->isConfigured()) {
            Log::warning(
                'Cloudinary delete skipped: credentials not configured.'
            );

            return false;
        }

        try {
            $this->configure();

            (new UploadApi())->destroy(
                $publicId
            );

            return true;
        } catch (Throwable $e) {
            Log::error(
                'Cloudinary delete failed',
                [
                    'public_id' => $publicId,
                    'message' => $e->getMessage(),
                ]
            );

            return false;
        }
    }

    private function configure(): void
    {
        $credentials = $this->getCredentials();

        Configuration::instance([
            'cloud' => [
                'cloud_name' => $credentials['cloud_name'],
                'api_key' => $credentials['api_key'],
                'api_secret' => $credentials['api_secret'],
            ],

            'url' => [
                'secure' => true,
            ],
        ]);
    }

    private function isConfigured(): bool
    {
        $credentials = $this->getCredentials();

        return filled($credentials['cloud_name'])
            && filled($credentials['api_key'])
            && filled($credentials['api_secret']);
    }

    private function getCredentials(): array
    {
        $cloudName = AppSettings::get('cloudinary_cloud_name');
        $apiKey = AppSettings::get('cloudinary_api_key');
        $apiSecret = AppSettings::get('cloudinary_api_secret');

        return [
            'cloud_name' => filled($cloudName)
                ? $cloudName
                : config('services.cloudinary.cloud_name'),
            'api_key' => filled($apiKey)
                ? $apiKey
                : config('services.cloudinary.api_key'),
            'api_secret' => filled($apiSecret)
                ? $apiSecret
                : config('services.cloudinary.api_secret'),
        ];
    }
}

