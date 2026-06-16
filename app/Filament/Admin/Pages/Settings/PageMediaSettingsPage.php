<?php

namespace App\Filament\Admin\Pages\Settings;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class PageMediaSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $title = 'Public Page Media Settings';

    protected static ?string $slug = 'settings/page-media';

    private const MEDIA_SETTINGS = [
        'media_destinations_hero_image_url' => [
            'label' => 'Destinasi Hero Image',
            'helper' => 'Background hero halaman /destinasi.',
        ],
        'media_reviews_hero_image_url' => [
            'label' => 'Reviews Hero Image',
            'helper' => 'Background hero halaman /reviews.',
        ],
        'media_packages_hero_fallback_image_url' => [
            'label' => 'Packages Hero Image',
            'helper' => 'Background hero halaman /paket jika belum ada paket bergambar.',
        ],
        'media_packages_empty_image_url' => [
            'label' => 'Packages Empty State Image',
            'helper' => 'Gambar saat belum ada paket aktif.',
        ],
        'media_package_card_fallback_1_url' => [
            'label' => 'Package Card Fallback 1',
            'helper' => 'Fallback kartu paket jika paket belum punya gambar.',
        ],
        'media_package_card_fallback_2_url' => [
            'label' => 'Package Card Fallback 2',
            'helper' => 'Fallback kartu paket variasi kedua.',
        ],
        'media_package_card_fallback_3_url' => [
            'label' => 'Package Card Fallback 3',
            'helper' => 'Fallback kartu paket variasi ketiga.',
        ],
        'media_guides_hero_fallback_image_url' => [
            'label' => 'Guides Hero Image',
            'helper' => 'Background hero halaman /panduan jika belum ada guide dengan foto.',
        ],
        'media_guides_empty_image_url' => [
            'label' => 'Guides Empty State Image',
            'helper' => 'Gambar saat belum ada guide aktif.',
        ],
    ];

    protected static function settingKeys(): array
    {
        return array_keys(self::MEDIA_SETTINGS);
    }

    protected function schema(): array
    {
        return [
            Section::make('Gambar Halaman Publik')
                ->description('Kontrol gambar global untuk halaman publik. Gambar destinasi/guide/paket spesifik tetap dikelola dari resource masing-masing.')
                ->schema($this->mediaSettingFields())
                ->columns(2),
        ];
    }

    protected function beforeSave(array &$data): void
    {
        foreach (array_keys(self::MEDIA_SETTINGS) as $key) {
            $this->handleSingleImageUpload($data, $key);
        }
    }

    private function mediaSettingFields(): array
    {
        $fields = [];

        foreach (self::MEDIA_SETTINGS as $key => $definition) {
            $fields[] = TextInput::make($key)
                ->label($definition['label'])
                ->helperText($definition['helper'])
                ->url()
                ->maxLength(2048);

            $fields[] = FileUpload::make($this->uploadFieldName($key))
                ->label('Upload ' . $definition['label'])
                ->image()
                ->disk('local')
                ->directory('tmp/settings-media')
                ->visibility('private')
                ->imageEditor(false)
                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                ->maxSize(4096)
                ->helperText('Opsional. Upload baru akan mengganti URL di field sebelahnya.')
                ->dehydrated(false);
        }

        return $fields;
    }
}
