<?php

namespace App\Filament\Admin\Pages\Settings;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class FooterSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $title = 'Footer';

    protected static ?string $slug = 'settings/footer';

    public function getTitle(): string
    {
        return '🦶 Pengaturan Footer (Bagian Bawah Website)';
    }

    public function getSubheading(): ?string
    {
        return 'Atur gambar yang muncul di bagian bawah website (footer). Gambar ini biasanya berisi ajakan terakhir untuk hubungi atau booking.';
    }

    protected static function settingKeys(): array
    {
        return [
            'media_footer_cta_image_url',
        ];
    }

    protected function schema(): array
    {
        return [
            Section::make('🎯 Gambar Ajakan di Footer')
                ->description('Gambar besar yang muncul di bagian paling bawah website sebelum navigasi akhir. Biasanya berisi ajakan untuk kontak, booking, atau follow media sosial.')
                ->icon('heroicon-m-rectangle-stack')
                ->schema([
                    TextInput::make('media_footer_cta_image_url')
                        ->label('🌐 URL Gambar Footer')
                        ->placeholder('https://example.com/image.jpg')
                        ->url()
                        ->maxLength(2048),
                    FileUpload::make($this->uploadFieldName('media_footer_cta_image_url'))
                        ->label('📤 Upload Gambar Footer')
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
                        ->helperText('Ukuran: maks 2 MB. Gunakan gambar landscape yang menarik.')
                        ->dehydrated(false),
                ])
                ->columns(1),
        ];
    }

    protected function beforeSave(array &$data): void
    {
        $this->handleSingleImageUpload($data, 'media_footer_cta_image_url');
    }
}
