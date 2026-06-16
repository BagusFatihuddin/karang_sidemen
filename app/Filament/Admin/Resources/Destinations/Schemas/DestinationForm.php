<?php

namespace App\Filament\Admin\Resources\Destinations\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
                Section::make('Konten Destinasi')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Destinasi')
                            ->required()
                            ->maxLength(150),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->helperText('Kosongkan saat membuat destinasi baru agar dibuat otomatis dari nama.')
                            ->maxLength(180),

                        Textarea::make('short_description')
                            ->label('Deskripsi Pendek')
                            ->rows(2)
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Deskripsi Panjang')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),

                        TextInput::make('tourism_vibe')
                            ->label('Vibe Wisata')
                            ->helperText('Contoh: tenang, sejuk, fotogenik, cocok untuk keluarga.')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('facilities')
                            ->label('Fasilitas')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Storytelling Homepage')
                    ->schema([
                        Toggle::make('is_featured_homepage')
                            ->label('Tampilkan di Homepage Cinematic')
                            ->helperText('Aktifkan untuk kartu reel, horizontal interruption, dan experience grid.')
                            ->default(false),

                        TextInput::make('homepage_sort_order')
                            ->label('Urutan Homepage')
                            ->numeric()
                            ->minValue(1),

                        TextInput::make('homepage_label')
                            ->label('Label Pendek Homepage')
                            ->helperText('Contoh: Blue Lake, Forest Escape, Hidden Camping.')
                            ->maxLength(80),

                        TagsInput::make('tags')
                            ->label('Tags')
                            ->separator(',')
                            ->columnSpanFull(),

                        TagsInput::make('highlights')
                            ->label('Highlight Features')
                            ->separator(',')
                            ->columnSpanFull(),

                        TagsInput::make('activity_keywords')
                            ->label('Activity Keywords')
                            ->separator(',')
                            ->helperText('Contoh: swimming, camping, waterfall, river, photography.')
                            ->columnSpanFull(),

                        TagsInput::make('source_urls')
                            ->label('Sumber Data Publik')
                            ->separator(',')
                            ->helperText('URL referensi agar konten tetap bisa dicek.')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Harga, Kontak, dan Lokasi')
                    ->schema([
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
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

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
                                                ->images
                                                ->map(function ($image) use ($record) {

                                                    $minOrder = $record
                                                        ->images
                                                        ->min('sort_order');

                                                    $maxOrder = $record
                                                        ->images
                                                        ->max('sort_order');

                                                    $canMoveUp =
                                                        $image->sort_order > 1;

                                                    $canMoveDown =
                                                        $image->sort_order <
                                                        $maxOrder;

                                                    return sprintf(
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

                                                            %s

                                                            %s

                                                            <div style="margin-top:8px;">
                                                                <button
                                                                    type="button"
                                                                    wire:click.prevent.stop="setCoverImage(%d)"
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
                                                                    wire:click.prevent.stop="deleteImage(%d)"
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

                                                        $canMoveUp
                                                            ? sprintf(
                                                                '
                                                                <div style="margin-top:8px;">
                                                                    <button
                                                                        type="button"
                                                                        wire:click.prevent.stop="moveImageUp(%d)"
                                                                        wire:loading.attr="disabled"
                                                                        wire:target="moveImageUp"
                                                                        style="
                                                                            width:100%%;
                                                                            padding:8px;
                                                                            border:1px solid #ddd;
                                                                            border-radius:8px;
                                                                            cursor:pointer;
                                                                        "
                                                                    >
                                                                        ↑ Naik
                                                                    </button>
                                                                </div>
                                                                ',
                                                                $image->id
                                                            )
                                                            : '',

                                                        $canMoveDown
                                                            ? sprintf(
                                                                '
                                                                <div style="margin-top:8px;">
                                                                    <button
                                                                        type="button"
                                                                        wire:click.prevent.stop="moveImageDown(%d)"
                                                                        wire:loading.attr="disabled"
                                                                        wire:target="moveImageDown"
                                                                        style="
                                                                            width:100%%;
                                                                            padding:8px;
                                                                            border:1px solid #ddd;
                                                                            border-radius:8px;
                                                                            cursor:pointer;
                                                                        "
                                                                    >
                                                                        ↓ Turun
                                                                    </button>
                                                                </div>
                                                                ',
                                                                $image->id
                                                            )
                                                            : '',

                                                        $image->id,
                                                        $image->id
                                                    );
                                                })
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
