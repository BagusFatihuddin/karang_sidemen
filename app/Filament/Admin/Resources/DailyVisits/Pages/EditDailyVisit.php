<?php

namespace App\Filament\Admin\Resources\DailyVisits\Pages;

use App\Filament\Admin\Resources\DailyVisits\DailyVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditDailyVisit extends EditRecord
{
    protected static string $resource = DailyVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['recorded_by'] = Auth::id();
        return $data;
    }
}
