<?php

namespace App\Filament\Admin\Resources\Promos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PromoForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Promo')
                    ->required()
                    ->maxLength(150),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(5)
                    ->columnSpanFull(),

                TextInput::make('external_url')
                    ->label('External URL')
                    ->url()
                    ->maxLength(500),

                DatePicker::make('start_date')
                    ->label('Tanggal Mulai'),

                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->rules(
                        fn (Get $get): array =>
                            filled($get('start_date'))
                                ? ['after_or_equal:start_date']
                                : []
                    ),

                FileUpload::make('promo_upload')
                    ->label('Upload Gambar')
                    ->image()
                    ->disk('local')
                    ->directory('tmp/promo-images')
                    ->visibility('private')
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
                    ->dehydrated(false)
                    ->columnSpanFull(),

                Placeholder::make('promo_image_preview')
                    ->label('Preview Gambar')
                    ->content(
                        fn ($record) => new HtmlString(
                            sprintf(
                                '<img src="%s" style="width:220px;height:140px;object-fit:cover;border-radius:8px;">',
                                e($record?->image_url)
                            )
                        )
                    )
                    ->visible(
                        fn (
                            $record,
                            string $operation
                        ): bool =>
                            $operation === 'edit'
                            && filled($record?->image_url)
                    )
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}
