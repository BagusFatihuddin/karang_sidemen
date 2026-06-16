<?php

namespace App\Filament\Admin\Resources\Guides\Pages;

use App\Filament\Admin\Resources\Guides\GuideResource;
use App\Services\CloudinaryService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateGuide extends CreateRecord
{
    protected static string $resource = GuideResource::class;

    protected function afterCreate(): void
    {
        $this->uploadGuidePhoto();
    }

    protected function uploadGuidePhoto(): void
    {
        $uploadState = $this->data['guide_upload'] ?? null;

        if (! is_array($uploadState)) {
            return;
        }

        $path = reset($uploadState);

        if (! $path) {
            return;
        }

        try {
            $fullPath = Storage::disk('local')->path($path);

            $uploadedFile = new UploadedFile(
                $fullPath,
                basename($fullPath),
                mime_content_type($fullPath) ?: 'image/jpeg',
                test: true
            );

            $result = app(CloudinaryService::class)->upload(
                $uploadedFile,
                'guides'
            );

            $this->record->update([
                'photo_url' => $result['url'],
                'photo_public_id' => $result['public_id'],
            ]);
        } catch (\Throwable $e) {
            Log::warning(
                'Guide photo upload failed on create',
                [
                    'guide_id' => $this->record->id,
                    'message' => $e->getMessage(),
                ]
            );

            Notification::make()
                ->title('Upload foto guide gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
