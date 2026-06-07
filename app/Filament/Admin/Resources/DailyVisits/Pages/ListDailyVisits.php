<?php

namespace App\Filament\Admin\Resources\DailyVisits\Pages;

use App\Filament\Admin\Resources\DailyVisits\DailyVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailyVisits extends ListRecords
{
    protected static string $resource = DailyVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
