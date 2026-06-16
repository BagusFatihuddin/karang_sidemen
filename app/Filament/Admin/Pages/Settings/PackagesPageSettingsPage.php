<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Filament\Admin\Pages\Settings\Concerns\BuildsMediaSettingFields;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class PackagesPageSettingsPage extends BaseSettingsPage
{
    use BuildsMediaSettingFields;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?string $title = 'Halaman Paket';

    protected static ?string $slug = 'settings/packages-page';

    public function getTitle(): string
    {
        return '🛍️ Pengaturan Halaman Paket Wisata';
    }

    public function getSubheading(): ?string
    {
        return 'Atur gambar untuk halaman daftar paket wisata. Gambar \"sampul\" akan muncul di atas, dan gambar cadangan (fallback) akan digunakan jika paket belum memiliki foto sendiri.';    }
    private const MEDIA_SETTINGS = [
        'media_packages_hero_fallback_image_url' => [
            'label' => 'Hero Image Paket',
            'helper' => 'Background hero halaman /paket jika belum ada paket bergambar.',
        ],
        'media_packages_empty_image_url' => [
            'label' => 'Empty State Image Paket',
            'helper' => 'Gambar saat belum ada paket aktif.',
        ],
        'media_package_card_fallback_1_url' => [
            'label' => 'Fallback Kartu Paket 1',
            'helper' => 'Fallback kartu paket jika paket belum punya gambar.',
        ],
        'media_package_card_fallback_2_url' => [
            'label' => 'Fallback Kartu Paket 2',
            'helper' => 'Fallback kartu paket variasi kedua.',
        ],
        'media_package_card_fallback_3_url' => [
            'label' => 'Fallback Kartu Paket 3',
            'helper' => 'Fallback kartu paket variasi ketiga.',
        ],
    ];

    protected static function settingKeys(): array
    {
        return array_keys(self::MEDIA_SETTINGS);
    }

    protected function schema(): array
    {
        return [
            Section::make('🎒 Gambar Halaman Paket')
                ->description('Kumpulkan semua gambar yang berhubungan dengan halaman paket: gambar pembuka, gambar fallback jika paket tidak punya foto. Gunakan gambar landscape yang menarik.')
                ->icon('heroicon-m-rectangle-stack')
                ->schema($this->mediaSettingFields(self::MEDIA_SETTINGS))
                ->columns(1),
        ];
    }

    protected function beforeSave(array &$data): void
    {
        $this->handleMediaSettingUploads($data, self::MEDIA_SETTINGS);
    }
}
