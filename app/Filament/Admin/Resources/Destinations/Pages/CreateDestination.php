<?php

namespace App\Filament\Admin\Resources\Destinations\Pages;

use App\Filament\Admin\Resources\Destinations\DestinationResource;
use App\Models\DestinationImage;
use App\Services\CloudinaryService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateDestination extends CreateRecord
{
    protected static string $resource =
        DestinationResource::class;

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {
        $data['cloudinary_folder'] =
            sprintf(
                'destinations/%s',
                Str::slug(
                    $data['name']
                )
            );

        return $data;
    }

protected function afterCreate(): void
{
    $uploadState =
        $this->data['destination_upload']
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
        $existingImage =
            $this->record
                ->images()
                ->latest('id')
                ->first();

        if ($existingImage) {
            try {
                app(
                    CloudinaryService::class
                )->delete(
                    $existingImage
                        ->cloudinary_public_id
                );
            } catch (\Throwable $e) {
                Log::warning(
                    'Destination image replace delete failed on create',
                    [
                        'destination_id' =>
                            $this->record->id,

                        'public_id' =>
                            $existingImage
                                ->cloudinary_public_id,

                        'message' =>
                            $e->getMessage(),
                    ]
                );
            }

            $existingImage->delete();
        }

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
            'destinations/general'
        );

        DestinationImage::create([
            'destination_id' =>
                $this->record->id,

            'cloudinary_public_id' =>
                $result['public_id'],

            'url' =>
                $result['url'],

            'sort_order' => 0,
        ]);
    } catch (\Throwable $e) {
        Log::warning(
            'Destination image upload failed on create',
            [
                'destination_id' =>
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