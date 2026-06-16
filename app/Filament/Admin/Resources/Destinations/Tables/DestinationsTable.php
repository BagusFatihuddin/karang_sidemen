<?php

namespace App\Filament\Admin\Resources\Destinations\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DestinationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Destinasi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('destination_type')
                    ->label('Jenis')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_featured_homepage')
                    ->label('Homepage')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('homepage_sort_order')
                    ->label('Urutan')
                    ->sortable(),

                TextColumn::make('entry_fee')
                    ->label('Biaya Masuk')
                    ->sortable(),

                TextColumn::make('whatsapp_number')
                    ->label('WhatsApp')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('destination_type')
                    ->label('Jenis Destinasi')
                    ->options([
                        'camping' => 'Camping',
                        'air' => 'Air',
                        'edukasi' => 'Edukasi',
                        'alam' => 'Alam',
                        'kuliner' => 'Kuliner',
                        'lainnya' => 'Lainnya',
                    ]),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),

                SelectFilter::make('is_featured_homepage')
                    ->label('Homepage')
                    ->options([
                        '1' => 'Tampil',
                        '0' => 'Tidak tampil',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
