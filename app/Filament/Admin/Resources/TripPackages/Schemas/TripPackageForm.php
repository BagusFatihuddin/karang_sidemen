<?php

namespace App\Filament\Admin\Resources\TripPackages\Schemas;

use App\Models\Destination;
use App\Models\Guide;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TripPackageForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Paket')
                    ->required()
                    ->maxLength(150),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(5)
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label('Harga')
                    ->numeric(),

                FileUpload::make('package_upload')
                    ->label('Upload Gambar')
                    ->image()
                    ->disk('local')
                    ->directory('tmp/trip-package-images')
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

                Placeholder::make('package_image_preview')
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

                Section::make('Destinasi dalam Paket')
                    ->schema([
                        Repeater::make('package_destinations')
                            ->label('Destinasi')
                            ->schema([
                                Select::make('destination_id')
                                    ->label('Destinasi')
                                    ->options(
                                        fn (): array => Destination::query()
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->all()
                                    )
                                    ->searchable()
                                    ->required()
                                    ->native(false)
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            ])
                            ->reorderable()
                            ->default([])
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Guide')
                    ->description('Pilih guide aktif yang akan tampil di kartu paket publik. Lengkapi foto dan keahlian guide di menu Guide Lokal.')
                    ->schema([
                        CheckboxList::make('package_guides')
                            ->label('Guide Pendamping')
                            ->options(
                                fn (): array => Guide::query()
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all()
                            )
                            ->columns(2)
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
