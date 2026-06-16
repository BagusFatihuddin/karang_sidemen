<?php

namespace App\Filament\Admin\Resources\Destinations\Pages;

use App\Filament\Admin\Resources\Destinations\DestinationResource;
use App\Models\DestinationImage;
use App\Services\CloudinaryService;
use App\Support\CacheVersion;
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
        $data['slug'] = filled($data['slug'] ?? null)
            ? Str::slug($data['slug'])
            : Str::slug($data['name']);

        $data['cloudinary_folder'] =
            sprintf(
                'destinations/%s',
                $data['slug']
            );

        return $data;
    }

    protected function afterCreate(): void
    {
        CacheVersion::bump('destinations:version');

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
            $currentImageCount =
                $this->record
                    ->images()
                    ->count();

            if ($currentImageCount >= 10) {
                Notification::make()
                    ->title(
                        'Batas maksimal gambar tercapai'
                    )
                    ->body(
                        'Maksimal 10 gambar per destinasi.'
                    )
                    ->danger()
                    ->send();

                return;
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
                $this->record->cloudinary_folder ?: 'destinations/general'
            );

            DestinationImage::create([
                'destination_id' =>
                    $this->record->id,

                'cloudinary_public_id' =>
                    $result['public_id'],

                'url' =>
                    $result['url'],

                'sort_order' =>
                    $currentImageCount === 0
                        ? 0
                        : (
                            ($this->record
                                ->images()
                                ->max('sort_order') ?? 0)
                            + 1
                        ),
            ]);

            CacheVersion::bump('destinations:version');
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
