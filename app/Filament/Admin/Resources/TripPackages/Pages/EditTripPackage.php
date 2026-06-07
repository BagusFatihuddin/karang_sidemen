<?php

namespace App\Filament\Admin\Resources\TripPackages\Pages;

use App\Filament\Admin\Resources\TripPackages\TripPackageResource;
use App\Services\CloudinaryService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditTripPackage extends EditRecord
{
    protected static string $resource = TripPackageResource::class;

    protected function mutateFormDataBeforeFill(
        array $data
    ): array {
        $data['package_destinations'] =
            $this->record
                ->destinations()
                ->orderByPivot('sort_order')
                ->get()
                ->map(
                    fn ($destination): array => [
                        'destination_id' =>
                            $destination->id,
                    ]
                )
                ->values()
                ->all();

        $data['package_guides'] =
            $this->record
                ->guides()
                ->pluck('guides.id')
                ->all();

        return $data;
    }

    protected function afterSave(): void
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

        $oldPublicId =
            $this->record->image_public_id;

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

            if ($oldPublicId) {
                app(CloudinaryService::class)->delete(
                    $oldPublicId
                );
            }

            Notification::make()
                ->title(
                    'Gambar berhasil di-upload'
                )
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Log::warning(
                'Trip package image upload failed',
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
