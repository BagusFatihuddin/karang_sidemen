<?php

namespace App\Filament\Admin\Resources\Guides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class GuideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profil Guide')
                    ->description('Data ini tampil di halaman Panduan publik dan relasi paket wisata.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Guide')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('experience')
                            ->label('Keahlian / Pengalaman')
                            ->placeholder('Contoh: Jalur air terjun, camping, cerita lokal')
                            ->maxLength(255),

                        Textarea::make('bio')
                            ->label('Bio Pendek')
                            ->rows(5)
                            ->maxLength(1200)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Foto Guide')
                    ->description('Gunakan foto asli guide. Jika kosong, halaman publik menampilkan inisial nama.')
                    ->schema([
                        FileUpload::make('guide_upload')
                            ->label('Upload Foto')
                            ->image()
                            ->disk('local')
                            ->directory('tmp/guide-images')
                            ->visibility('private')
                            ->imageEditor(false)
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/jpg',
                                'image/png',
                                'image/webp',
                            ])
                            ->maxSize(2048)
                            ->helperText('Format: JPG, PNG, WEBP. Maksimal 2MB.')
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Placeholder::make('guide_photo_preview')
                            ->label('Preview Foto')
                            ->content(
                                fn ($record) => new HtmlString(
                                    sprintf(
                                        '<img src="%s" style="width:160px;height:200px;object-fit:cover;border-radius:10px;">',
                                        e($record?->photo_url)
                                    )
                                )
                            )
                            ->visible(
                                fn ($record, string $operation): bool =>
                                    $operation === 'edit'
                                    && filled($record?->photo_url)
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
