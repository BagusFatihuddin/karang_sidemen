<?php

namespace App\Filament\Admin\Resources\DailyVisits\Pages;

use App\Filament\Admin\Resources\DailyVisits\DailyVisitResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDailyVisit extends CreateRecord
{
    protected static string $resource = DailyVisitResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by'] = Auth::id();
        return $data;
    }
}
