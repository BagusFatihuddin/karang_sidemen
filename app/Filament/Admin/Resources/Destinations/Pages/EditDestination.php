<?php

namespace App\Filament\Admin\Resources\Destinations\Pages;

use App\Filament\Admin\Resources\Destinations\DestinationResource;
use App\Models\DestinationImage;
use App\Services\CloudinaryService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditDestination extends EditRecord
{
    protected static string $resource =
        DestinationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make(
                'deleteImage'
            )
                ->label(
                    'Hapus Gambar'
                )
                ->color('danger')
                ->requiresConfirmation()
                ->visible(
                    fn (): bool =>
                        $this->record
                            ->images()
                            ->exists()
                )
                ->action(
                    function (): void {
                        $image =
                            $this->record
                                ->images()
                                ->latest('id')
                                ->first();

                        if (! $image) {
                            return;
                        }

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
                ),
        ];
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
                    'Destination image replace delete failed',
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