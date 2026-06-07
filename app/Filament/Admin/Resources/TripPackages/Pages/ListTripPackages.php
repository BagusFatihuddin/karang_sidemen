<?php

namespace App\Filament\Admin\Resources\TripPackages\Pages;

use App\Filament\Admin\Resources\TripPackages\TripPackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTripPackages extends ListRecords
{
    protected static string $resource = TripPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
