<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Filament\Admin\Pages\Settings\Concerns\BuildsMediaSettingFields;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ReviewsPageSettingsPage extends BaseSettingsPage
{
    use BuildsMediaSettingFields;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $title = 'Halaman Review';

    protected static ?string $slug = 'settings/reviews-page';

    public function getTitle(): string
    {
        return '⭐ Pengaturan Halaman Review';
    }

    public function getSubheading(): ?string
    {
        return 'Atur gambar yang muncul di bagian atas halaman daftar review pengunjung. Gambar ini menunjukkan apa kata pengunjung tentang desa wisata Anda.';
    }

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
            Section::make('🖼️ Gambar Pembuka Halaman Review')
                ->description('Gambar besar yang pengunjung lihat pertama kali saat masuk ke halaman review. Pilih gambar yang menunjukkan kebahagiaan dan kepuasan pengunjung.')
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
