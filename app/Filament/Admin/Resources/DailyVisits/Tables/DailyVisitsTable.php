<?php

namespace App\Filament\Admin\Resources\DailyVisits\Tables;

use App\Models\Destination;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DailyVisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('destination.name')
                    ->label('Destinasi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d-M-Y')
                    ->sortable(),

                TextColumn::make('visitor_count')
                    ->label('Wisatawan')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('revenue')
                    ->label('Pemasukan')
                    ->numeric()
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('expense')
                    ->label('Pengeluaran')
                    ->numeric()
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('recordedBy.name')
                    ->label('Dicatat Oleh')
                    ->placeholder('-'),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d-M-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('destination_id')
                    ->label('Destinasi')
                    ->options(
                        fn (): array => Destination::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all()
                    )
                    ->searchable(),

                Filter::make('date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('date_from')
                            ->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('date_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'] ?? null,
                                fn (Builder $q, $date) => $q->whereDate('date', '>=', $date)
                            )
                            ->when(
                                $data['date_until'] ?? null,
                                fn (Builder $q, $date) => $q->whereDate('date', '<=', $date)
                            );
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->paginated([25, 50, 100]);
    }
}
