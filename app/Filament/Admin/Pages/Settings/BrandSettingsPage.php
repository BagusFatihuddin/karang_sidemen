<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Filament\Admin\Components\BrandLogoPreview;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class BrandSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $title = 'Brand & Logo';

    protected static ?string $slug = 'settings/brand';

    public function getTitle(): string
    {
        return '🎨 Brand & Logo';
    }

    public function getSubheading(): ?string
    {
        return 'Upload dan atur logo yang akan muncul di bagian atas website (navbar) dan bawah website (footer).';
    }

    protected static function settingKeys(): array
    {
        return [
            'brand_mark_text',
            'brand_logo_url',
            'brand_logo_alt',
        ];
    }

    protected function schema(): array
    {
        return [
            Section::make('📌 Logo Situs')
                ->description('Logo yang akan muncul di navbar (bagian atas) dan footer (bagian bawah) halaman website publik.')
                ->icon('heroicon-m-squares-2x2')
                ->schema([
                    BrandLogoPreview::make(),

                    TextInput::make('brand_logo_alt')
                        ->label('Deskripsi Logo')
                        ->placeholder('Contoh: Logo Karang Sidemen')
                        ->helperText('Deskripsi untuk membantu aksesibilitas. Tidak terlihat di website.')
                        ->maxLength(255),

                    FileUpload::make($this->uploadFieldName('brand_logo_url'))
                        ->label('📤 Upload Logo Baru')
                        ->image()
                        ->disk('local')
                        ->directory('tmp/settings-media')
                        ->visibility('private')
                        ->imageEditor(false)
                        ->acceptedFileTypes([
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/webp',
                            'image/svg+xml',
                        ])
                        ->maxSize(2048)
                        ->helperText('Format: JPG, PNG, WebP, atau SVG. Ukuran: max 2 MB. Ukuran yang direkomendasikan: 200x200 px.')
                        ->dehydrated(false),
                ])
                ->columns(1),

            Section::make('📝 Pengaturan Lanjutan')
                ->description('Jika logo belum diupload, teks ini akan ditampilkan sebagai pengganti.')
                ->icon('heroicon-m-cog-6-tooth')
                ->schema([
                    TextInput::make('brand_mark_text')
                        ->label('Teks Pengganti Logo')
                        ->placeholder('Contoh: KS')
                        ->helperText('Teks singkat jika logo tidak ada. Gunakan 2-3 huruf saja.')
                        ->maxLength(12),
                ])
                ->columns(1),
        ];
    }

    protected function beforeSave(array &$data): void
    {
        $this->handleSingleImageUpload($data, 'brand_logo_url', 'website-brand');
    }
}
