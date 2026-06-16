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
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('destination_type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'camping' => 'warning',
                        'air' => 'info',
                        'edukasi' => 'success',
                        'alam' => 'success',
                        'kuliner' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),

                IconColumn::make('is_featured_homepage')
                    ->label('Homepage')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),

                TextColumn::make('homepage_sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->visible(fn (): bool => false), // Hidden by default, can be shown in columns menu

                TextColumn::make('entry_fee')
                    ->label('Biaya Masuk')
                    ->sortable()
                    ->formatStateUsing(fn (?float $state): string => $state ? 'Rp ' . number_format($state, 0, ',', '.') : '—'),

                TextColumn::make('whatsapp_number')
                    ->label('WhatsApp')
                    ->searchable()
                    ->url(fn (string $state): string => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $state), shouldOpenInNewTab: true)
                    ->color('info'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->size('sm')
                    ->color('gray'),
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
