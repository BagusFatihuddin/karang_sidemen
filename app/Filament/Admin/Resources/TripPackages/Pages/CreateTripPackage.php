<?php

namespace App\Filament\Admin\Resources\TripPackages\Pages;

use App\Filament\Admin\Resources\TripPackages\TripPackageResource;
use App\Services\CloudinaryService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateTripPackage extends CreateRecord
{
    protected static string $resource = TripPackageResource::class;

    protected function afterCreate(): void
    {
        $this->uploadPackageImage();
        $this->syncPackageRelations();
    }

    protected function uploadPackageImage(): void
    {
        $uploadState =
            $this->data['package_upload']
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
                'trip-packages'
            );

            $this->record->update([
                'image_url' =>
                    $result['url'],

                'image_public_id' =>
                    $result['public_id'],
            ]);
        } catch (\Throwable $e) {
            Log::warning(
                'Trip package image upload failed on create',
                [
                    'trip_package_id' =>
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

    protected function syncPackageRelations(): void
    {
        $destinations = [];

        foreach (
            array_values($this->data['package_destinations'] ?? [])
            as $index => $destination
        ) {
            $destinationId =
                $destination['destination_id']
                ?? null;

            if (! $destinationId) {
                continue;
            }

            $destinations[$destinationId] = [
                'sort_order' => $index,
            ];
        }

        $this->record
            ->destinations()
            ->sync($destinations);

        $this->record
            ->guides()
            ->sync($this->data['package_guides'] ?? []);
    }
}
