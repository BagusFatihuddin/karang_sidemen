<?php

namespace App\Filament\Admin\Pages\Settings;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class HomepageSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $title = 'Halaman Utama';

    protected static ?string $slug = 'settings/homepage';

    public function getTitle(): string
    {
        return '🏠 Pengaturan Halaman Utama (Homepage)';
    }

    public function getSubheading(): ?string
    {
        return 'Atur teks dan gambar yang akan muncul di halaman depan website. Halaman ini adalah kesan pertama pengunjung terhadap desa wisata Anda.';
    }

    protected static function settingKeys(): array
    {
        return [
            'homepage_hero_eyebrow',
            'homepage_hero_title_line_1',
            'homepage_hero_title_line_2',
            'homepage_hero_cta_label',
            'media_homepage_hero_image_url',
            'homepage_reel_eyebrow',
            'homepage_reel_title',
            'homepage_portal_eyebrow',
            'homepage_portal_title',
            'homepage_portal_body',
            'homepage_breathing_eyebrow',
            'homepage_horizontal_eyebrow',
            'homepage_horizontal_title',
            'homepage_horizontal_hint',
            'homepage_experience_eyebrow',
            'homepage_experience_title',
            'homepage_highlight_eyebrow',
            'homepage_highlight_title',
            'homepage_reviews_eyebrow',
            'homepage_reviews_title',
            'homepage_final_eyebrow',
            'homepage_final_title',
            'homepage_final_cta_label',
            'media_homepage_final_image_url',
        ];
    }

    protected function schema(): array
    {
        return [
            Section::make('Section 1 - Hero Pembuka')
                ->description('Teks dan gambar utama saat pengunjung pertama kali membuka homepage.')
                ->schema([
                    TextInput::make('homepage_hero_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                    TextInput::make('homepage_hero_title_line_1')
                        ->label('Judul Baris 1')
                        ->maxLength(80),
                    TextInput::make('homepage_hero_title_line_2')
                        ->label('Judul Baris 2')
                        ->maxLength(80),
                    TextInput::make('homepage_hero_cta_label')
                        ->label('Label Tombol')
                        ->maxLength(80),
                    TextInput::make('media_homepage_hero_image_url')
                        ->label('Hero Image Beranda URL')
                        ->helperText('Gambar utama homepage. Jika kosong, sistem memakai gambar destinasi unggulan sebagai fallback.')
                        ->url()
                        ->maxLength(2048)
                        ->columnSpanFull(),
                    FileUpload::make($this->uploadFieldName('media_homepage_hero_image_url'))
                        ->label('Upload Hero Image Beranda')
                        ->image()
                        ->disk('local')
                        ->directory('tmp/settings-media')
                        ->visibility('private')
                        ->imageEditor(false)
                        ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                        ->maxSize(4096)
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Section 2 - Moving Reel')
                ->description('Bagian reel gambar bergerak yang menjelaskan Karang Sidemen sebagai desa wisata, bukan satu spot saja.')
                ->schema([
                    TextInput::make('homepage_reel_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                    Textarea::make('homepage_reel_title')
                        ->label('Judul')
                        ->rows(2),
                ]),

            Section::make('Section 3 - Zoom / Portal Reveal')
                ->description('Bagian zoom reveal. Token {portal} dan {next} akan diganti otomatis dengan nama destinasi yang sedang tampil.')
                ->schema([
                    TextInput::make('homepage_portal_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                    Textarea::make('homepage_portal_title')
                        ->label('Judul Portal')
                        ->helperText('Contoh: Masuk ke {portal}. Keluar lagi ke {next}.')
                        ->rows(2),
                    Textarea::make('homepage_portal_body')
                        ->label('Deskripsi Singkat')
                        ->rows(3),
                ]),

            Section::make('Section 4 - Breathing Moment')
                ->description('Momen jeda sebelum interaksi horizontal agar pengalaman scroll tidak terlalu padat.')
                ->schema([
                    TextInput::make('homepage_breathing_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                ]),

            Section::make('Section 5 - Horizontal Immersive Explore')
                ->description('Bagian scroll vertikal yang terasa bergerak ke samping.')
                ->schema([
                    TextInput::make('homepage_horizontal_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                    Textarea::make('homepage_horizontal_title')
                        ->label('Judul')
                        ->helperText('Catatan: teks ini muncul di pembuka section horizontal, bukan di setiap kartu destinasi.')
                        ->rows(2),
                    TextInput::make('homepage_horizontal_hint')
                        ->label('Hint Scroll')
                        ->maxLength(120),
                ]),

            Section::make('Section 6 - Database-driven Experiences')
                ->description('Bagian kartu destinasi dari data database.')
                ->schema([
                    TextInput::make('homepage_experience_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                    Textarea::make('homepage_experience_title')
                        ->label('Judul')
                        ->rows(2),
                ]),

            Section::make('Section 7 - Highlight Desa')
                ->description('Bagian highlight besar tentang kategori pengalaman wisata.')
                ->schema([
                    TextInput::make('homepage_highlight_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                    Textarea::make('homepage_highlight_title')
                        ->label('Judul')
                        ->rows(2),
                ]),

            Section::make('Section 8 - Review')
                ->description('Bagian social proof atau suara pengunjung.')
                ->schema([
                    TextInput::make('homepage_reviews_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                    Textarea::make('homepage_reviews_title')
                        ->label('Judul')
                        ->rows(2),
                ]),

            Section::make('Section 9 - Final CTA')
                ->description('Penutup homepage sebelum footer.')
                ->schema([
                    TextInput::make('homepage_final_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                    Textarea::make('homepage_final_title')
                        ->label('Judul')
                        ->rows(2),
                    TextInput::make('homepage_final_cta_label')
                        ->label('Label Tombol')
                        ->maxLength(80),
                    TextInput::make('media_homepage_final_image_url')
                        ->label('Gambar Final CTA URL')
                        ->url()
                        ->maxLength(2048),
                    FileUpload::make($this->uploadFieldName('media_homepage_final_image_url'))
                        ->label('Upload Gambar Final CTA')
                        ->image()
                        ->disk('local')
                        ->directory('tmp/settings-media')
                        ->visibility('private')
                        ->imageEditor(false)
                        ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                        ->maxSize(4096)
                        ->dehydrated(false),
                ]),
        ];
    }

    protected function beforeSave(array &$data): void
    {
        $this->handleSingleImageUpload($data, 'media_homepage_hero_image_url');
        $this->handleSingleImageUpload($data, 'media_homepage_final_image_url');
    }
}
