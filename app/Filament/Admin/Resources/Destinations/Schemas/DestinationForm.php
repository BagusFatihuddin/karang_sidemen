<?php

namespace App\Filament\Admin\Resources\Destinations\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class DestinationForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Destinasi')
                    ->required()
                    ->maxLength(150),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                Textarea::make('facilities')
                    ->label('Fasilitas')
                    ->rows(4)
                    ->columnSpanFull(),

                Select::make('destination_type')
                    ->label('Jenis Destinasi')
                    ->options([
                        'camping' => 'Camping',
                        'air' => 'Air',
                        'edukasi' => 'Edukasi',
                        'alam' => 'Alam',
                        'kuliner' => 'Kuliner',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required()
                    ->native(false),

                TextInput::make('entry_fee')
                    ->label('Biaya Masuk')
                    ->numeric(),

                TextInput::make('parking_fee')
                    ->label('Biaya Parkir')
                    ->numeric(),

                TextInput::make('rental_price')
                    ->label('Harga Sewa')
                    ->numeric(),

                TextInput::make('whatsapp_number')
                    ->label('WhatsApp')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('maps_url')
                    ->label('Google Maps URL')
                    ->url()
                    ->maxLength(500),

                Section::make(
                    'Gambar Destinasi'
                )
                    ->schema([
                        Placeholder::make(
                            'current_destination_image'
                        )
                            ->label(
                                'Gambar Saat Ini'
                            )
                            ->content(
                                fn ($record) => new HtmlString(
                                    $record?->images()
                                        ->latest('id')
                                        ->value('url')
                                        ? sprintf(
                                            '<img src="%s" style="max-width: 300px; border-radius: 12px;">',
                                            $record
                                                ->images()
                                                ->latest('id')
                                                ->value('url')
                                        )
                                        : '<span>Belum ada gambar</span>'
                                )
                            )
                            ->visible(
                                fn (
                                    string $operation
                                ): bool =>
                                    $operation === 'edit'
                            ),

                        FileUpload::make(
                            'destination_upload'
                        )
                            ->label(
                                'Upload Gambar'
                            )
                            ->image()
                            ->disk('local')
                            ->directory(
                                'tmp/destination-images'
                            )
                            ->visibility(
                                'private'
                            )
                            ->imageEditor(false)
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/jpg',
                                'image/png',
                                'image/webp',
                            ])
                            ->maxSize(2048)
                            ->helperText(
                                'Format: JPG, PNG, WEBP. Maksimal 2MB.'
                            )
                            ->dehydrated(false),
                    ])
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}
