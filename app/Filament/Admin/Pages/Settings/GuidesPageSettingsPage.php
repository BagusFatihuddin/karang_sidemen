<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Filament\Admin\Pages\Settings\Concerns\BuildsMediaSettingFields;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class GuidesPageSettingsPage extends BaseSettingsPage
{
    use BuildsMediaSettingFields;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $title = 'Halaman Panduan';

    protected static ?string $slug = 'settings/guides-page';

    public function getTitle(): string
    {
        return '👥 Pengaturan Halaman Panduan (Guide Lokal)';
    }

    public function getSubheading(): ?string
    {
        return 'Atur gambar untuk halaman daftar panduan (guide lokal). Gambar fallback akan digunakan jika guide belum memiliki foto sendiri.';
    }

    private const MEDIA_SETTINGS = [
        'media_guides_hero_fallback_image_url' => [
            'label' => 'Hero Image Panduan',
            'helper' => 'Background hero halaman /panduan jika belum ada guide dengan foto.',
        ],
        'media_guides_empty_image_url' => [
            'label' => 'Empty State Image Panduan',
            'helper' => 'Gambar saat belum ada guide aktif.',
        ],
        'media_guides_note_image_url' => [
            'label' => 'Gambar Card Kenapa Pakai Guide',
            'helper' => 'Gambar pendukung untuk card "Kenapa pakai guide?" di halaman /panduan.',
        ],
    ];

    protected static function settingKeys(): array
    {
        return array_keys(self::MEDIA_SETTINGS);
    }

    protected function schema(): array
    {
        return [
            Section::make('👨‍🏫 Gambar Halaman Panduan')
                ->description('Kumpulkan semua gambar untuk halaman panduan: gambar pembuka, gambar fallback jika guide tidak punya foto, dan gambar untuk kartu \"Kenapa pakai guide?\".')
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
