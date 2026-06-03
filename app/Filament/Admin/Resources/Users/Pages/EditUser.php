<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(
                    fn (): bool =>
                        Auth::id() !== $this->record->id
                )
                ->before(function (): void {
                    if (Auth::id() === $this->record->id) {
                        Notification::make()
                            ->title('Anda tidak dapat menghapus akun sendiri.')
                            ->danger()
                            ->send();

                        $this->halt();
                    }
                }),
        ];
    }
}