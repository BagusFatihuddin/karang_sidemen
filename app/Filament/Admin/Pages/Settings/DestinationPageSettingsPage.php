<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Filament\Admin\Pages\Settings\Concerns\BuildsMediaSettingFields;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class DestinationPageSettingsPage extends BaseSettingsPage
{
    use BuildsMediaSettingFields;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $title = 'Halaman Destinasi';

    protected static ?string $slug = 'settings/destinations-page';

    public function getTitle(): string
    {
        return '📍 Pengaturan Halaman Destinasi';
    }

    public function getSubheading(): ?string
    {
        return 'Atur gambar yang muncul di bagian atas halaman daftar destinasi. Ini adalah gambar \"sampul\" untuk halaman destinasi.';    }
    private const MEDIA_SETTINGS = [
        'media_destinations_hero_image_url' => [
            'label' => 'Hero Image Destinasi',
            'helper' => 'Background hero halaman /destinasi.',
        ],
    ];

    protected static function settingKeys(): array
    {
        return array_keys(self::MEDIA_SETTINGS);
    }

    protected function schema(): array
    {
        return [
            Section::make('🖼️ Gambar Pembuka Halaman Destinasi')
                ->description('Gambar besar yang pengunjung lihat pertama kali saat masuk ke halaman daftar destinasi. Pilih gambar yang menarik dan representatif untuk desa wisata.')
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
