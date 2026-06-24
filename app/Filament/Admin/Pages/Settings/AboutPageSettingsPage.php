<?php

namespace App\Filament\Admin\Pages\Settings;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class AboutPageSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;

    protected static ?string $title = 'Halaman Tentang';

    protected static ?string $slug = 'settings/about';

    public function getTitle(): string
    {
        return 'ℹ️ Pengaturan Halaman Tentang';
    }

    public function getSubheading(): ?string
    {
        return 'Atur gambar, cerita, struktur organisasi, dan peta untuk halaman \"Tentang Kami\". Halaman ini membantu pengunjung memahami desa wisata Anda lebih baik.';
    }

    protected static function settingKeys(): array
    {
        return [
            'media_about_hero_fallback_image_url',
            'media_about_story_image_url',
            'media_about_organization_chart_image_url',
            'about_organization_title',
            'google_maps_embed_url',
        ];
    }

    protected function schema(): array
    {
        return [
            Section::make('Hero Halaman Tentang')
                ->description('Gambar pembuka halaman /tentang.')
                ->schema([
                    TextInput::make('media_about_hero_fallback_image_url')
                        ->label('Hero Image URL')
                        ->url()
                        ->maxLength(2048),
                    FileUpload::make($this->uploadFieldName('media_about_hero_fallback_image_url'))
                        ->label('Upload Hero Image')
                        ->image()
                        ->disk('local')
                        ->directory('tmp/settings-media')
                        ->visibility('private')
                        ->imageEditor(false)
                        ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                        ->maxSize(2048)
                        ->validationMessages([
                            'mimetypes' => 'Tipe file tidak sesuai. Gunakan gambar JPG, PNG, atau WEBP.',
                            'max' => 'Ukuran gambar terlalu besar. Maksimal 2 MB.',
                        ])
                        ->dehydrated(false),
                ])
                ->columns(2),

            Section::make('Gambar Cerita Lokal')
                ->description('Gambar pendamping bagian cerita “wisata desa harus terasa hidup”.')
                ->schema([
                    TextInput::make('media_about_story_image_url')
                        ->label('Story Image URL')
                        ->url()
                        ->maxLength(2048),
                    FileUpload::make($this->uploadFieldName('media_about_story_image_url'))
                        ->label('Upload Story Image')
                        ->image()
                        ->disk('local')
                        ->directory('tmp/settings-media')
                        ->visibility('private')
                        ->imageEditor(false)
                        ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                        ->maxSize(2048)
                        ->validationMessages([
                            'mimetypes' => 'Tipe file tidak sesuai. Gunakan gambar JPG, PNG, atau WEBP.',
                            'max' => 'Ukuran gambar terlalu besar. Maksimal 2 MB.',
                        ])
                        ->dehydrated(false),
                ])
                ->columns(2),

            Section::make('Struktur Organisasi POKDARWIS')
                ->description('Opsional. Jika gambar struktur organisasi kosong, section ini nanti tidak perlu ditampilkan di halaman publik.')
                ->schema([
                    TextInput::make('about_organization_title')
                        ->label('Judul Section')
                        ->maxLength(255),
                    TextInput::make('media_about_organization_chart_image_url')
                        ->label('Gambar Struktur Organisasi URL')
                        ->url()
                        ->maxLength(2048),
                    FileUpload::make($this->uploadFieldName('media_about_organization_chart_image_url'))
                        ->label('Upload Struktur Organisasi')
                        ->image()
                        ->disk('local')
                        ->directory('tmp/settings-media')
                        ->visibility('private')
                        ->imageEditor(false)
                        ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                        ->maxSize(2048)
                        ->validationMessages([
                            'mimetypes' => 'Tipe file tidak sesuai. Gunakan gambar JPG, PNG, atau WEBP.',
                            'max' => 'Ukuran gambar terlalu besar. Maksimal 2 MB.',
                        ])
                        ->dehydrated(false),
                ])
                ->columns(2),

            Section::make('Lokasi / Peta')
                ->description('Embed atau URL Google Maps yang dipakai di halaman Tentang dan kontak lokasi.')
                ->schema([
                    Textarea::make('google_maps_embed_url')
                        ->label('Google Maps Embed / Search URL')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ];
    }

    protected function beforeSave(array &$data): void
    {
        $this->handleSingleImageUpload($data, 'media_about_hero_fallback_image_url');
        $this->handleSingleImageUpload($data, 'media_about_story_image_url');
        $this->handleSingleImageUpload($data, 'media_about_organization_chart_image_url');
    }
}
