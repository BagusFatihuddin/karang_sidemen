<?php

namespace App\Filament\Admin\Resources\Bookings\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                    ->sortable(),

                TextColumn::make('visitor.name')
                    ->label('Wisatawan')
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
            ]);
    }
}
