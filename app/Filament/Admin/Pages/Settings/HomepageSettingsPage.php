<?php

namespace App\Filament\Admin\Pages\Settings;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
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
            'homepage_zoom_items',
            'homepage_breathing_eyebrow',
            'homepage_breathing_title',
            'homepage_breathing_body',
            'media_homepage_breathing_image_url',
            'homepage_horizontal_eyebrow',
            'homepage_horizontal_title',
            'homepage_horizontal_hint',
            'homepage_horizontal_items',
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
                        ->maxSize(2048)
                        ->validationMessages([
                            'mimetypes' => 'Tipe file tidak sesuai. Gunakan gambar JPG, PNG, atau WEBP.',
                            'max' => 'Ukuran gambar terlalu besar. Maksimal 2 MB.',
                        ])
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
                ->description('Kurasi momen zoom homepage secara mandiri. Konten ini tidak lagi mengambil cover dari data destinasi.')
                ->schema([
                    TextInput::make('homepage_portal_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                    Repeater::make('homepage_zoom_items')
                        ->label('Zoom Story Items')
                        ->helperText('Setiap item adalah satu momen perjalanan. Gunakan gambar zoom-out untuk suasana luas dan zoom-in untuk detail eksplorasi.')
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true),
                            TextInput::make('display_order')
                                ->label('Urutan Tampil')
                                ->numeric()
                                ->default(1),
                            TextInput::make('title')
                                ->label('Judul Zoom')
                                ->placeholder('Contoh: Danau Biru')
                                ->maxLength(120)
                                ->required(),
                            Textarea::make('description')
                                ->label('Subtitle / Deskripsi')
                                ->rows(3)
                                ->columnSpanFull(),
                            TextInput::make('zoom_out_image_url')
                                ->label('Zoom-Out Image URL')
                                ->helperText('Wide landscape shot. Bisa diisi URL manual atau lewat upload di bawah.')
                                ->url()
                                ->maxLength(2048)
                                ->columnSpanFull(),
                            FileUpload::make('zoom_out_image_upload')
                                ->label('Upload Zoom-Out Image')
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
                                ->dehydrated(false)
                                ->columnSpanFull(),
                            TextInput::make('zoom_in_image_url')
                                ->label('Zoom-In Image URL')
                                ->helperText('Close-up/detail shot. Gunakan asset berbeda dari zoom-out agar transisi terasa eksploratif.')
                                ->url()
                                ->maxLength(2048)
                                ->columnSpanFull(),
                            FileUpload::make('zoom_in_image_upload')
                                ->label('Upload Zoom-In Image')
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
                                ->dehydrated(false)
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->default([])
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Zoom item')
                        ->columnSpanFull(),
                ]),

            Section::make('Section 4 - Breathing Moment')
                ->description('Momen jeda sebelum interaksi horizontal. Konten dan gambar bisa dikurasi dari homepage settings.')
                ->schema([
                    TextInput::make('homepage_breathing_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                    TextInput::make('homepage_breathing_title')
                        ->label('Judul')
                        ->maxLength(140),
                    Textarea::make('homepage_breathing_body')
                        ->label('Deskripsi')
                        ->rows(3)
                        ->columnSpanFull(),
                    TextInput::make('media_homepage_breathing_image_url')
                        ->label('Gambar Breathing Moment URL')
                        ->url()
                        ->maxLength(2048)
                        ->columnSpanFull(),
                    FileUpload::make($this->uploadFieldName('media_homepage_breathing_image_url'))
                        ->label('Upload Gambar Breathing Moment')
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
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Section 5 - Horizontal Immersive Explore')
                ->description('Bagian scroll vertikal yang terasa bergerak ke samping. Kartu cerita bisa dikelola tanpa mengubah data destinasi.')
                ->schema([
                    TextInput::make('homepage_horizontal_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                    Textarea::make('homepage_horizontal_title')
                        ->label('Judul')
                        ->helperText('Teks pembuka section horizontal.')
                        ->rows(2),
                    TextInput::make('homepage_horizontal_hint')
                        ->label('Hint Scroll')
                        ->maxLength(120),
                    Repeater::make('homepage_horizontal_items')
                        ->label('Horizontal Story Items')
                        ->helperText('Setiap kartu adalah cerita visual di section horizontal. Link bisa dikosongkan jika kartu tidak perlu bisa diklik.')
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true),
                            TextInput::make('display_order')
                                ->label('Urutan Tampil')
                                ->numeric()
                                ->default(1),
                            TextInput::make('title')
                                ->label('Judul Kartu')
                                ->maxLength(140)
                                ->required(),
                            TextInput::make('link_url')
                                ->label('Link Kartu')
                                ->helperText('Contoh: /destinasi/3 atau https://...')
                                ->maxLength(2048),
                            Textarea::make('description')
                                ->label('Deskripsi Kartu')
                                ->rows(3)
                                ->columnSpanFull(),
                            TextInput::make('image_url')
                                ->label('Gambar Kartu URL')
                                ->url()
                                ->maxLength(2048)
                                ->columnSpanFull(),
                            FileUpload::make('image_upload')
                                ->label('Upload Gambar Kartu')
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
                                ->dehydrated(false)
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->default([])
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Horizontal item')
                        ->columnSpanFull(),
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
                        ->maxSize(2048)
                        ->validationMessages([
                            'mimetypes' => 'Tipe file tidak sesuai. Gunakan gambar JPG, PNG, atau WEBP.',
                            'max' => 'Ukuran gambar terlalu besar. Maksimal 2 MB.',
                        ])
                        ->dehydrated(false),
                ]),
        ];
    }

    protected function beforeSave(array &$data): void
    {
        $this->handleSingleImageUpload($data, 'media_homepage_hero_image_url');
        $this->handleSingleImageUpload($data, 'media_homepage_breathing_image_url');
        $this->handleSingleImageUpload($data, 'media_homepage_final_image_url');

        $data['homepage_zoom_items'] = $this->normalizeZoomItems(
            $data['homepage_zoom_items'] ?? [],
            $this->data['homepage_zoom_items'] ?? []
        );
        $data['homepage_horizontal_items'] = $this->normalizeHorizontalItems(
            $data['homepage_horizontal_items'] ?? [],
            $this->data['homepage_horizontal_items'] ?? []
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<int, array<string, mixed>>  $rawItems
     * @return array<int, array<string, mixed>>
     */
    private function normalizeZoomItems(array $items, array $rawItems): array
    {
        $normalized = [];
        $rawItems = array_values($rawItems);

        foreach (array_values($items) as $index => $item) {
            $rawItem = $rawItems[$index] ?? [];

            $zoomOutUpload = $item['zoom_out_image_upload']
                ?? $rawItem['zoom_out_image_upload']
                ?? null;
            $zoomInUpload = $item['zoom_in_image_upload']
                ?? $rawItem['zoom_in_image_upload']
                ?? null;

            if ($zoomOutUrl = $this->uploadSettingsImageFromState(
                $zoomOutUpload,
                "homepage_zoom_items.{$index}.zoom_out_image_upload",
                'homepage-zoom'
            )) {
                $item['zoom_out_image_url'] = $zoomOutUrl;
            }

            if ($zoomInUrl = $this->uploadSettingsImageFromState(
                $zoomInUpload,
                "homepage_zoom_items.{$index}.zoom_in_image_upload",
                'homepage-zoom'
            )) {
                $item['zoom_in_image_url'] = $zoomInUrl;
            }

            unset($item['zoom_out_image_upload'], $item['zoom_in_image_upload']);

            $title = trim((string) ($item['title'] ?? ''));
            $zoomOutImageUrl = trim((string) ($item['zoom_out_image_url'] ?? ''));
            $zoomInImageUrl = trim((string) ($item['zoom_in_image_url'] ?? ''));

            if ($title === '' && $zoomOutImageUrl === '' && $zoomInImageUrl === '') {
                continue;
            }

            $normalized[] = [
                'title' => $title,
                'description' => trim((string) ($item['description'] ?? '')),
                'zoom_out_image_url' => $zoomOutImageUrl,
                'zoom_in_image_url' => $zoomInImageUrl,
                'display_order' => (int) ($item['display_order'] ?? ($index + 1)),
                'is_active' => (bool) ($item['is_active'] ?? true),
            ];
        }

        usort(
            $normalized,
            fn (array $a, array $b): int => $a['display_order'] <=> $b['display_order']
                ?: strcmp($a['title'], $b['title'])
        );

        return array_values($normalized);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<int, array<string, mixed>>  $rawItems
     * @return array<int, array<string, mixed>>
     */
    private function normalizeHorizontalItems(array $items, array $rawItems): array
    {
        $normalized = [];
        $rawItems = array_values($rawItems);

        foreach (array_values($items) as $index => $item) {
            $rawItem = $rawItems[$index] ?? [];
            $imageUpload = $item['image_upload'] ?? $rawItem['image_upload'] ?? null;

            if ($imageUrl = $this->uploadSettingsImageFromState(
                $imageUpload,
                "homepage_horizontal_items.{$index}.image_upload",
                'homepage-horizontal'
            )) {
                $item['image_url'] = $imageUrl;
            }

            unset($item['image_upload']);

            $title = trim((string) ($item['title'] ?? ''));
            $imageUrl = trim((string) ($item['image_url'] ?? ''));
            $description = trim((string) ($item['description'] ?? ''));

            if ($title === '' && $imageUrl === '' && $description === '') {
                continue;
            }

            $normalized[] = [
                'title' => $title,
                'description' => $description,
                'image_url' => $imageUrl,
                'link_url' => trim((string) ($item['link_url'] ?? '')),
                'display_order' => (int) ($item['display_order'] ?? ($index + 1)),
                'is_active' => (bool) ($item['is_active'] ?? true),
            ];
        }

        usort(
            $normalized,
            fn (array $a, array $b): int => $a['display_order'] <=> $b['display_order']
                ?: strcmp($a['title'], $b['title'])
        );

        return array_values($normalized);
    }
}
