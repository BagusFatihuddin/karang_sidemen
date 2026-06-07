<?php

namespace App\Filament\Admin\Resources\Bookings\Tables;

use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('booking_code')
                    ->label('Kode Booking')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kode booking disalin.')
                    ->sortable(),

                TextColumn::make('guest_name')
                    ->label('Wisatawan')
                    ->state(
                        fn (Booking $record): string =>
                        $record->visitor?->name
                            ?? $record->guest_name
                            ?? '-'
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('destination.name')
                    ->label('Destinasi')
                    ->sortable(),

                TextColumn::make('checkin_date')
                    ->label('Check-in')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('checkout_date')
                    ->label('Check-out')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'Pending',
                            'confirmed' => 'Confirmed',
                            'cancelled' => 'Cancelled',
                            'completed' => 'Completed',
                            default => $state ?? '-',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'warning',
                            'confirmed' => 'info',
                            'cancelled' => 'danger',
                            'completed' => 'success',
                            default => 'gray',
                        }
                    )
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ]),

                SelectFilter::make('destination_id')
                    ->label('Destinasi')
                    ->relationship(
                        name: 'destination',
                        titleAttribute: 'name'
                    )
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('confirm')
                    ->label('Confirm')
                    ->visible(
                        fn (Booking $record): bool =>
                        $record->status === 'pending'
                    )
                    ->requiresConfirmation()
                    ->action(function (Booking $record): void {
                        $record->update([
                            'status' => 'confirmed',
                        ]);

                        Notification::make()
                            ->title('Booking confirmed.')
                            ->success()
                            ->send();
                    }),

                Action::make('complete')
                    ->label('Complete')
                    ->visible(
                        fn (Booking $record): bool =>
                        $record->status === 'confirmed'
                    )
                    ->requiresConfirmation()
                    ->action(function (Booking $record): void {
                        $record->update([
                            'status' => 'completed',
                            'arrived_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Booking completed.')
                            ->success()
                            ->send();
                    }),

                Action::make('cancel')
                    ->label('Cancel')
                    ->color('danger')
                    ->visible(
                        fn (Booking $record): bool => in_array(
                            $record->status,
                            [
                                'pending',
                                'confirmed',
                            ],
                            true
                        )
                    )
                    ->requiresConfirmation()
                    ->action(function (Booking $record): void {
                        $record->update([
                            'status' => 'cancelled',
                        ]);

                        Notification::make()
                            ->title('Booking cancelled.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
