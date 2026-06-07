<?php

namespace App\Filament\Admin\Resources\Promos\Tables;

use App\Models\Promo;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PromosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Promo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status_badge')
                    ->label('Status')
                    ->badge()
                    ->state(
                        fn (Promo $record): string =>
                            static::status($record)
                    )
                    ->color(
                        fn (Promo $record): string =>
                            match (static::status($record)) {
                                'Aktif' => 'success',
                                'Kadaluarsa' => 'danger',
                                'Terjadwal' => 'info',
                                default => 'gray',
                            }
                    ),

                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    protected static function status(Promo $promo): string
    {
        if (! $promo->is_active) {
            return 'Nonaktif';
        }

        if (
            $promo->end_date
            && $promo->end_date->isBefore(today())
        ) {
            return 'Kadaluarsa';
        }

        if (
            (! $promo->start_date || $promo->start_date->isToday() || $promo->start_date->isPast())
            && (! $promo->end_date || $promo->end_date->isToday() || $promo->end_date->isFuture())
        ) {
            return 'Aktif';
        }

        return 'Terjadwal';
    }
}
