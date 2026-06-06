<?php

namespace App\Filament\Admin\Resources\Destinations\Pages;

use App\Filament\Admin\Resources\Destinations\DestinationResource;
use App\Models\DestinationImage;
use App\Services\CloudinaryService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditDestination extends EditRecord
{
    protected static string $resource =
        DestinationResource::class;

    public function setCoverImage(
        int $imageId
    ): void {
        $selectedImage =
            $this->record
                ->images()
                ->find($imageId);

        if (! $selectedImage) {
            return;
        }

        $images =
            $this->record
                ->images()
                ->orderBy('sort_order')
                ->get();

        $sortOrder = 1;

        foreach ($images as $image) {
            if (
                $image->id ===
                $selectedImage->id
            ) {
                continue;
            }

            $image->update([
                'sort_order' =>
                    $sortOrder++,
            ]);
        }

        $selectedImage->update([
            'sort_order' => 0,
        ]);

        Notification::make()
            ->title(
                'Cover image berhasil diubah'
            )
            ->success()
            ->send();

        $this->redirect(
            static::getResource()::getUrl(
                'edit',
                [
                    'record' =>
                        $this->record,
                ]
            )
        );
    }

    public function deleteImage(
        int $imageId
    ): void {
        $image =
            $this->record
                ->images()
                ->find($imageId);

        if (! $image) {
            return;
        }

        $wasCover =
            $image->sort_order === 0;

        try {
            app(
                CloudinaryService::class
            )->delete(
                $image->cloudinary_public_id
            );
        } catch (\Throwable $e) {
            Log::warning(
                'Destination image delete failed',
                [
                    'destination_id' =>
                        $this->record->id,

                    'public_id' =>
                        $image->cloudinary_public_id,

                    'message' =>
                        $e->getMessage(),
                ]
            );
        }

        $image->delete();

        if ($wasCover) {
            $fallbackImage =
                $this->record
                    ->images()
                    ->oldest('created_at')
                    ->first();

            if ($fallbackImage) {
                $remainingImages =
                    $this->record
                        ->images()
                        ->where(
                            'id',
                            '!=',
                            $fallbackImage->id
                        )
                        ->orderBy(
                            'sort_order'
                        )
                        ->get();

                $fallbackImage->update([
                    'sort_order' => 0,
                ]);

                $sortOrder = 1;

                foreach (
                    $remainingImages
                    as $remainingImage
                ) {
                    $remainingImage->update([
                        'sort_order' =>
                            $sortOrder++,
                    ]);
                }
            }
        }

        Notification::make()
            ->title(
                'Gambar berhasil dihapus'
            )
            ->success()
            ->send();

        $this->redirect(
            static::getResource()::getUrl(
                'edit',
                [
                    'record' =>
                        $this->record,
                ]
            )
        );
    }

    protected function afterSave(): void
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
                'destinations/general'
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

            Notification::make()
                ->title(
                    'Gambar berhasil di-upload'
                )
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Log::warning(
                'Destination image upload failed',
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