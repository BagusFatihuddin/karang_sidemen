<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Filament\Admin\Pages\Settings\Concerns\BuildsMediaSettingFields;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class DestinationPageSettingsPage extends BaseSettingsPage
{
    use BuildsMediaSettingFields;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $title = 'Pengaturan Halaman Destinasi';

    protected static ?string $slug = 'settings/destinations-page';

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
            Section::make('Media Halaman Destinasi')
                ->description('Gambar global untuk halaman daftar destinasi. Gambar tiap destinasi tetap diatur dari resource Destinasi.')
                ->schema($this->mediaSettingFields(self::MEDIA_SETTINGS))
                ->columns(2),
        ];
    }

    protected function beforeSave(array &$data): void
    {
        $this->handleMediaSettingUploads($data, self::MEDIA_SETTINGS);
    }
}
