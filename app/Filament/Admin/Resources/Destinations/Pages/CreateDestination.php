<?php

namespace App\Filament\Admin\Resources\Destinations\Pages;

use App\Filament\Admin\Resources\Destinations\DestinationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateDestination extends CreateRecord
{
    protected static string $resource = DestinationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['cloudinary_folder'] = sprintf(
            'destinations/%s',
            Str::slug($data['name'])
        );

        return $data;
    }
}