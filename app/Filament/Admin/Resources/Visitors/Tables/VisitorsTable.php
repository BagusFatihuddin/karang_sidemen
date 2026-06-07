<?php

namespace App\Filament\Admin\Resources\Visitors\Tables;

use App\Filament\Admin\Resources\Visitors\Exporters\VisitorExporter;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VisitorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('visited_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('origin_category')
                    ->label('Kategori Asal')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'lombok_tengah' => 'Lombok Tengah',
                            'lombok_lainnya' => 'Lombok Lainnya',
                            'luar_lombok' => 'Luar Lombok',
                            'mancanegara' => 'Mancanegara',
                            default => $state ?? '-',
                        }
                    )
                    ->sortable(),

                TextColumn::make('origin_city')
                    ->label('Kota Asal')
                    ->sortable(),

                TextColumn::make('visit_type')
                    ->label('Tipe Kunjungan')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'sendiri' => 'Sendiri',
                            'pasangan' => 'Pasangan',
                            'keluarga' => 'Keluarga',
                            'rombongan' => 'Rombongan',
                            default => $state ?? '-',
                        }
                    )
                    ->sortable(),

                TextColumn::make('destination.name')
                    ->label('Destinasi')
                    ->sortable(),

                TextColumn::make('recordedBy.name')
                    ->label('Dicatat Oleh')
                    ->sortable(),

                TextColumn::make('visited_at')
                    ->label('Tanggal Kunjungan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('origin_category')
                    ->label('Kategori Asal')
                    ->options([
                        'lombok_tengah' => 'Lombok Tengah',
                        'lombok_lainnya' => 'Lombok Lainnya',
                        'luar_lombok' => 'Luar Lombok',
                        'mancanegara' => 'Mancanegara',
                    ]),

                SelectFilter::make('visit_type')
                    ->label('Tipe Kunjungan')
                    ->options([
                        'sendiri' => 'Sendiri',
                        'pasangan' => 'Pasangan',
                        'keluarga' => 'Keluarga',
                        'rombongan' => 'Rombongan',
                    ]),

                SelectFilter::make('destination_id')
                    ->label('Destinasi')
                    ->relationship(
                        name: 'destination',
                        titleAttribute: 'name'
                    )
                    ->searchable()
                    ->preload(),

                Filter::make('visited_at')
                    ->label('Tanggal Kunjungan')
                    ->schema([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),

                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (
                        Builder $query,
                        array $data
                    ): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (
                                    Builder $query,
                                    $date
                                ): Builder => $query->whereDate(
                                    'visited_at',
                                    '>=',
                                    $date
                                )
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (
                                    Builder $query,
                                    $date
                                ): Builder => $query->whereDate(
                                    'visited_at',
                                    '<=',
                                    $date
                                )
                            );
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Export')
                    ->exporter(VisitorExporter::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}