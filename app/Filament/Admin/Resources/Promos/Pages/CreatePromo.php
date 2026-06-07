<?php

namespace App\Filament\Admin\Resources\Promos\Pages;

use App\Filament\Admin\Resources\Promos\PromoResource;
use App\Services\CloudinaryService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreatePromo extends CreateRecord
{
    protected static string $resource = PromoResource::class;

    protected function afterCreate(): void
    {
        $uploadState =
            $this->data['promo_upload']
            ?? null;

        if (! is_array($uploadState)) {
            return;
        }

        $path =
            reset($uploadState);

        if (! $path) {
            return;
        }

        try {
            $fullPath =
                Storage::disk('local')
                    ->path($path);

            $uploadedFile =
                new UploadedFile(
                    $fullPath,
                    basename($fullPath),
                    mime_content_type(
                        $fullPath
                    ),
                    test: true
                );

            $result = app(
                CloudinaryService::class
            )->upload(
                $uploadedFile,
                'promos'
            );

            $this->record->update([
                'image_url' =>
                    $result['url'],

                'image_public_id' =>
                    $result['public_id'],
            ]);
        } catch (\Throwable $e) {
            Log::warning(
                'Promo image upload failed on create',
                [
                    'promo_id' =>
                        $this->record->id,

                    'message' =>
                        $e->getMessage(),
                ]
            );

            Notification::make()
                ->title(
                    'Upload gambar gagal'
                )
                ->body(
                    $e->getMessage()
                )
                ->danger()
                ->send();
        }
    }
}
