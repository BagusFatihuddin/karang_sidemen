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
                            'destination_gallery'
                        )
                            ->label(
                                'Gallery Gambar'
                            )
                            ->content(
                                fn ($record) => new HtmlString(
                                    $record?->images()->exists()
                                        ? sprintf(
                                            '<div style="display:flex;flex-wrap:wrap;gap:16px;">%s</div>',
                                            $record
                                                ->images()
                                                ->orderBy(
                                                    'sort_order'
                                                )
                                                ->get()
                                                ->map(
                                                    fn ($image) =>
                                                        sprintf(
                                                            '
                                                            <div style="
                                                                border:1px solid #ddd;
                                                                border-radius:12px;
                                                                padding:10px;
                                                                width:180px;
                                                            ">
                                                                <img
                                                                    src="%s"
                                                                    style="
                                                                        width:100%%;
                                                                        height:140px;
                                                                        object-fit:cover;
                                                                        border-radius:8px;
                                                                    "
                                                                >

                                                                <div style="
                                                                    margin-top:8px;
                                                                    text-align:center;
                                                                    font-size:12px;
                                                                    font-weight:bold;
                                                                ">
                                                                    %s
                                                                </div>

                                                                <div style="margin-top:8px;">
                                                                    <button
                                                                        type="button"
                                                                        wire:click="setCoverImage(%d)"
                                                                        wire:loading.attr="disabled"
                                                                        wire:target="setCoverImage"
                                                                        style="
                                                                            width:100%%;
                                                                            padding:8px;
                                                                            border:1px solid #666;
                                                                            border-radius:8px;
                                                                            cursor:pointer;
                                                                        "
                                                                    >
                                                                        Set as Cover
                                                                    </button>
                                                                </div>

                                                                <div style="margin-top:8px;">
                                                                    <button
                                                                        type="button"
                                                                        wire:confirm="Yakin ingin menghapus gambar ini?"
                                                                        wire:click="deleteImage(%d)"
                                                                        wire:loading.attr="disabled"
                                                                        wire:target="deleteImage"
                                                                        style="
                                                                            width:100%%;
                                                                            padding:8px;
                                                                            border:1px solid #cc0000;
                                                                            border-radius:8px;
                                                                            cursor:pointer;
                                                                        "
                                                                    >
                                                                        <span
                                                                            wire:loading.remove
                                                                            wire:target="deleteImage"
                                                                        >
                                                                            Delete
                                                                        </span>

                                                                        <span
                                                                            wire:loading
                                                                            wire:target="deleteImage"
                                                                        >
                                                                            Deleting...
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            ',
                                                            $image->url,
                                                            $image->sort_order === 0
                                                                ? '⭐ Cover Image'
                                                                : 'Gallery Image',
                                                            $image->id,
                                                            $image->id
                                                        )
                                                )
                                                ->implode('')
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