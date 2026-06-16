<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Filament\Admin\Pages\Settings\Concerns\BuildsMediaSettingFields;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ReviewsPageSettingsPage extends BaseSettingsPage
{
    use BuildsMediaSettingFields;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $title = 'Pengaturan Halaman Review';

    protected static ?string $slug = 'settings/reviews-page';

    private const MEDIA_SETTINGS = [
        'media_reviews_hero_image_url' => [
            'label' => 'Hero Image Review',
            'helper' => 'Background hero halaman /reviews.',
        ],
    ];

    protected static function settingKeys(): array
    {
        return array_keys(self::MEDIA_SETTINGS);
    }

    protected function schema(): array
    {
        return [
            Section::make('Media Halaman Review')
                ->description('Gambar global untuk halaman kumpulan review.')
                ->schema($this->mediaSettingFields(self::MEDIA_SETTINGS))
                ->columns(2),
        ];
    }

    protected function beforeSave(array &$data): void
    {
        $this->handleMediaSettingUploads($data, self::MEDIA_SETTINGS);
    }
}
