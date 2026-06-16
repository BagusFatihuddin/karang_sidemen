<?php

namespace App\Filament\Admin\Resources\Guides\Pages;

use App\Filament\Admin\Resources\Guides\GuideResource;
use App\Services\CloudinaryService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditGuide extends EditRecord
{
    protected static string $resource = GuideResource::class;

    protected function afterSave(): void
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

        $oldPublicId = $this->record->photo_public_id;

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

            if ($oldPublicId) {
                app(CloudinaryService::class)->delete($oldPublicId);
            }

            Notification::make()
                ->title('Foto guide berhasil di-upload')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Log::warning(
                'Guide photo upload failed',
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
