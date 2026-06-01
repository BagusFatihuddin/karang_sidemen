<?php

namespace App\Services;

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
                    'quality' => 'auto:good',
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
        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],

            'url' => [
                'secure' => true,
            ],
        ]);
    }

    private function isConfigured(): bool
    {
        return filled(
            config('services.cloudinary.cloud_name')
        )
            && filled(
                config('services.cloudinary.api_key')
            )
            && filled(
                config('services.cloudinary.api_secret')
            );
    }
}