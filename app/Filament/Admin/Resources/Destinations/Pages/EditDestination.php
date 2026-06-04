<?php

namespace App\Filament\Admin\Resources\Destinations\Pages;

use App\Filament\Admin\Resources\Destinations\DestinationResource;
use Filament\Resources\Pages\EditRecord;

class EditDestination extends EditRecord
{
    protected static string $resource =
        DestinationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}