<?php

namespace App\Filament\Admin\Resources\Visitors\Infolists;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisitorInfolist
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([
                Section::make('Informasi Wisatawan')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama'),

                        TextEntry::make('whatsapp_number')
                            ->label('WhatsApp'),

                        TextEntry::make('origin_category')
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
                            ),

                        TextEntry::make('origin_city')
                            ->label('Kota Asal'),

                        TextEntry::make('visit_type')
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
                            ),

                        TextEntry::make('group_size')
                            ->label('Jumlah Grup'),

                        TextEntry::make('destination.name')
                            ->label('Destinasi')
                            ->placeholder('-'),

                        TextEntry::make('recordedBy.name')
                            ->label('Dicatat Oleh')
                            ->placeholder('-'),

                        TextEntry::make('referral_source')
                            ->label('Sumber Tahu')
                            ->formatStateUsing(
                                fn (?string $state): string => match ($state) {
                                    'instagram' => 'Instagram',
                                    'whatsapp' => 'WhatsApp',
                                    'teman' => 'Teman',
                                    'google' => 'Google',
                                    'lainnya' => 'Lainnya',
                                    default => $state ?? '-',
                                }
                            ),

                        TextEntry::make('referral_other')
                            ->label('Sumber Lainnya')
                            ->placeholder('-'),

                        TextEntry::make('visited_at')
                            ->label('Tanggal Kunjungan')
                            ->dateTime('d M Y H:i'),

                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}