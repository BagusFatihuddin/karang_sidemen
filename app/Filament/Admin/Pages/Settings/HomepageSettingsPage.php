<?php

namespace App\Filament\Admin\Pages\Settings;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

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
            Html::make(fn (): HtmlString => $this->builderIntro()),

            Tabs::make('Homepage Builder')
                ->persistTabInQueryString('homepage-section')
                ->tabs([
                    Tab::make('Konten Utama')
                        ->icon(Heroicon::OutlinedSparkles)
                        ->schema([
                            $this->heroSection(),
                            $this->movingTextSection(),
                        ]),
                    Tab::make('Zoom Moments')
                        ->icon(Heroicon::OutlinedPhoto)
                        ->schema([
                            $this->zoomMomentsSection(),
                        ]),
                    Tab::make('Section Lanjutan')
                        ->icon(Heroicon::OutlinedSquares2x2)
                        ->schema([
                            $this->breathingSection(),
                            $this->horizontalSection(),
                            $this->supportingSections(),
                        ]),
                ])
                ->contained(false),
        ];
    }

    private function heroSection(): Section
    {
        return Section::make('Hero Section')
            ->description('Konten pembuka yang pertama kali dilihat pengunjung. Fokuskan pada headline, pesan utama, dan visual paling kuat.')
            ->icon(Heroicon::OutlinedHome)
            ->schema([
                Grid::make([
                    'default' => 1,
                    'lg' => 5,
                ])->schema([
                    Section::make('Preview visual')
                        ->description('Gunakan gambar sinematik dengan komposisi luas. Preview mengikuti URL gambar yang sedang tersimpan.')
                        ->schema([
                            Html::make(fn (): HtmlString => $this->imagePreviewCard(
                                $this->data['media_homepage_hero_image_url'] ?? null,
                                'Hero image preview',
                                'Gambar hero belum diatur. Upload atau paste URL gambar untuk melihat preview.'
                            )),
                            FileUpload::make($this->uploadFieldName('media_homepage_hero_image_url'))
                                ->label('Upload Hero Image')
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
                            TextInput::make('media_homepage_hero_image_url')
                                ->label('Hero Image URL')
                                ->helperText('Bisa memakai upload di atas atau paste URL gambar langsung.')
                                ->url()
                                ->maxLength(2048),
                        ])
                        ->compact()
                        ->columnSpan(['lg' => 2]),

                    Section::make('Copy utama')
                        ->description('Tulis seperti headline landing page, bukan label database.')
                        ->schema([
                            TextInput::make('homepage_hero_eyebrow')
                                ->label('Eyebrow / Label kecil')
                                ->maxLength(255),
                            Grid::make(2)->schema([
                                TextInput::make('homepage_hero_title_line_1')
                                    ->label('Judul Baris 1')
                                    ->maxLength(80),
                                TextInput::make('homepage_hero_title_line_2')
                                    ->label('Judul Baris 2')
                                    ->maxLength(80),
                            ]),
                            TextInput::make('homepage_hero_cta_label')
                                ->label('Label CTA lama')
                                ->helperText('Disimpan untuk kompatibilitas. Tombol Zoom saat ini tidak ditampilkan di hero.')
                                ->maxLength(80),
                            Placeholder::make('hero_copy_preview')
                                ->label('Preview teks')
                                ->content(fn (): HtmlString => $this->copyPreview(
                                    trim(($this->data['homepage_hero_title_line_1'] ?? 'Karang') . ' ' . ($this->data['homepage_hero_title_line_2'] ?? 'Sidemen')),
                                    $this->data['homepage_hero_eyebrow'] ?? 'POKDARWIS Karang Sidemen'
                                )),
                        ])
                        ->compact()
                        ->columnSpan(['lg' => 3]),
                ]),
            ]);
    }

    private function movingTextSection(): Section
    {
        return Section::make('Moving Text Section')
            ->description('Bagian reel pembuka cerita. Dibuat ringkas agar admin langsung melihat pesan yang sedang tayang.')
            ->icon(Heroicon::OutlinedBars3BottomLeft)
            ->schema([
                Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])->schema([
                    Placeholder::make('moving_text_preview')
                        ->label('Current preview')
                        ->content(fn (): HtmlString => $this->copyPreview(
                            $this->data['homepage_reel_title'] ?? 'Karang Sidemen punya beberapa pengalaman alam yang saling nyambung.',
                            $this->data['homepage_reel_eyebrow'] ?? 'Desa wisata, bukan satu spot'
                        ))
                        ->columnSpan(['md' => 1]),
                    Section::make('Edit teks')
                        ->schema([
                            TextInput::make('homepage_reel_eyebrow')
                                ->label('Eyebrow')
                                ->maxLength(255),
                            Textarea::make('homepage_reel_title')
                                ->label('Judul moving text')
                                ->rows(3),
                        ])
                        ->compact()
                        ->columnSpan(['md' => 2]),
                ]),
            ]);
    }

    private function zoomMomentsSection(): Section
    {
        return Section::make('Zoom Moments Section')
            ->description('Kelola momen zoom seperti koleksi kartu cerita. Item dibuat collapsed agar admin tidak harus melihat semua field sekaligus.')
            ->icon(Heroicon::OutlinedPhoto)
            ->schema([
                TextInput::make('homepage_portal_eyebrow')
                    ->label('Label kecil section')
                    ->helperText('Contoh: Scroll zoom moment')
                    ->maxLength(255),
                Repeater::make('homepage_zoom_items')
                    ->label('Moment cards')
                    ->helperText('Klik kartu untuk edit detail. Drag atau tombol reorder untuk mengubah urutan tampil.')
                    ->schema([
                        Html::make(fn (Get $get): HtmlString => $this->zoomMomentCardPreview([
                            'title' => $get('title'),
                            'description' => $get('description'),
                            'zoom_out_image_url' => $get('zoom_out_image_url'),
                            'zoom_in_image_url' => $get('zoom_in_image_url'),
                            'display_order' => $get('display_order'),
                            'is_active' => $get('is_active'),
                        ]))
                            ->columnSpanFull(),
                        Section::make('Informasi moment')
                            ->description('Info ini muncul sebagai copy utama pada section Zoom di homepage.')
                            ->schema([
                                Grid::make([
                                    'default' => 1,
                                    'md' => 4,
                                ])->schema([
                                    Toggle::make('is_active')
                                        ->label('Aktif')
                                        ->default(true),
                                    TextInput::make('display_order')
                                        ->label('Urutan')
                                        ->numeric()
                                        ->default(1),
                                    TextInput::make('title')
                                        ->label('Judul Moment')
                                        ->placeholder('Contoh: Danau Biru')
                                        ->maxLength(120)
                                        ->required()
                                        ->columnSpan(['md' => 2]),
                                ]),
                                Textarea::make('description')
                                    ->label('Subtitle / Deskripsi')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->compact()
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])->schema([
                            Section::make('Zoom-Out Image')
                                ->description('Wide landscape shot.')
                                ->schema([
                                    TextInput::make('zoom_out_image_url')
                                        ->label('Image URL')
                                        ->url()
                                        ->maxLength(2048),
                                    FileUpload::make('zoom_out_image_upload')
                                        ->label('Upload')
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
                                ])
                                ->compact(),
                            Section::make('Zoom-In Image')
                                ->description('Close-up/detail shot.')
                                ->schema([
                                    TextInput::make('zoom_in_image_url')
                                        ->label('Image URL')
                                        ->url()
                                        ->maxLength(2048),
                                    FileUpload::make('zoom_in_image_upload')
                                        ->label('Upload')
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
                                ])
                                ->compact(),
                        ]),
                    ])
                    ->columns(1)
                    ->grid([
                        'default' => 1,
                        'xl' => 2,
                    ])
                    ->default([])
                    ->collapsed()
                    ->collapsible()
                    ->cloneable()
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->addActionLabel('Tambah Zoom Moment')
                    ->itemLabel(fn (array $state): string => $this->zoomItemLabel($state))
                    ->columnSpanFull(),
            ]);
    }

    private function breathingSection(): Section
    {
        return Section::make('Breathing Moment')
            ->description('Momen jeda sebelum horizontal story.')
            ->icon(Heroicon::OutlinedCloud)
            ->collapsed()
            ->schema([
                Grid::make([
                    'default' => 1,
                    'lg' => 5,
                ])->schema([
                    Section::make('Visual')
                        ->schema([
                            Html::make(fn (): HtmlString => $this->imagePreviewCard(
                                $this->data['media_homepage_breathing_image_url'] ?? null,
                                'Breathing image preview',
                                'Gambar breathing moment belum diatur.'
                            )),
                            FileUpload::make($this->uploadFieldName('media_homepage_breathing_image_url'))
                                ->label('Upload Gambar')
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
                            TextInput::make('media_homepage_breathing_image_url')
                                ->label('Image URL')
                                ->url()
                                ->maxLength(2048),
                        ])
                        ->compact()
                        ->columnSpan(['lg' => 2]),
                    Section::make('Copy')
                        ->schema([
                            TextInput::make('homepage_breathing_eyebrow')
                                ->label('Eyebrow')
                                ->maxLength(255),
                            TextInput::make('homepage_breathing_title')
                                ->label('Judul')
                                ->maxLength(140),
                            Textarea::make('homepage_breathing_body')
                                ->label('Deskripsi')
                                ->rows(3),
                        ])
                        ->compact()
                        ->columnSpan(['lg' => 3]),
                ]),
            ]);
    }

    private function horizontalSection(): Section
    {
        return Section::make('Horizontal Immersive Explore')
            ->description('Kartu cerita horizontal setelah breathing moment.')
            ->icon(Heroicon::OutlinedRectangleStack)
            ->collapsed()
            ->schema([
                Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])->schema([
                    TextInput::make('homepage_horizontal_eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(255),
                    Textarea::make('homepage_horizontal_title')
                        ->label('Judul section')
                        ->rows(2),
                    TextInput::make('homepage_horizontal_hint')
                        ->label('Hint Scroll')
                        ->maxLength(120),
                ]),
                Repeater::make('homepage_horizontal_items')
                    ->label('Horizontal Story Cards')
                    ->schema([
                        Html::make(fn (Get $get): HtmlString => $this->horizontalCardPreview([
                            'title' => $get('title'),
                            'description' => $get('description'),
                            'image_url' => $get('image_url'),
                            'link_url' => $get('link_url'),
                            'display_order' => $get('display_order'),
                            'is_active' => $get('is_active'),
                        ]))
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                            'md' => 4,
                        ])->schema([
                            Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true),
                            TextInput::make('display_order')
                                ->label('Urutan')
                                ->numeric()
                                ->default(1),
                            TextInput::make('title')
                                ->label('Judul Kartu')
                                ->maxLength(140)
                                ->required()
                                ->columnSpan(['md' => 2]),
                        ]),
                        TextInput::make('link_url')
                            ->label('Link Kartu')
                            ->helperText('Contoh: /destinasi/3 atau https://...')
                            ->maxLength(2048),
                        Textarea::make('description')
                            ->label('Deskripsi Kartu')
                            ->rows(3),
                        TextInput::make('image_url')
                            ->label('Gambar Kartu URL')
                            ->url()
                            ->maxLength(2048),
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
                            ->dehydrated(false),
                    ])
                    ->grid([
                        'default' => 1,
                        'xl' => 2,
                    ])
                    ->collapsed()
                    ->collapsible()
                    ->cloneable()
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->addActionLabel('Tambah Story Card')
                    ->itemLabel(fn (array $state): string => $this->horizontalItemLabel($state))
                    ->columnSpanFull(),
            ]);
    }

    private function supportingSections(): Section
    {
        return Section::make('Supporting Homepage Copy')
            ->description('Konten pendukung setelah section utama. Tetap tersedia, tapi dibuat collapsed agar halaman builder tidak terasa penuh.')
            ->icon(Heroicon::OutlinedDocumentText)
            ->collapsed()
            ->schema([
                Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])->schema([
                    Section::make('Database-driven Experiences')
                        ->schema([
                            TextInput::make('homepage_experience_eyebrow')
                                ->label('Eyebrow')
                                ->maxLength(255),
                            Textarea::make('homepage_experience_title')
                                ->label('Judul')
                                ->rows(2),
                        ])
                        ->compact(),
                    Section::make('Highlight Desa')
                        ->schema([
                            TextInput::make('homepage_highlight_eyebrow')
                                ->label('Eyebrow')
                                ->maxLength(255),
                            Textarea::make('homepage_highlight_title')
                                ->label('Judul')
                                ->rows(2),
                        ])
                        ->compact(),
                    Section::make('Review')
                        ->schema([
                            TextInput::make('homepage_reviews_eyebrow')
                                ->label('Eyebrow')
                                ->maxLength(255),
                            Textarea::make('homepage_reviews_title')
                                ->label('Judul')
                                ->rows(2),
                        ])
                        ->compact(),
                    Section::make('Final CTA')
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
                        ])
                        ->compact(),
                ]),
            ]);
    }

    private function builderIntro(): HtmlString
    {
        return new HtmlString(<<<'HTML'
            <div class="rounded-2xl border border-primary-200/60 bg-gradient-to-br from-primary-50 via-white to-amber-50 p-5 shadow-sm dark:border-primary-500/20 dark:from-primary-950/30 dark:via-white/5 dark:to-amber-950/10">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-primary-700 dark:text-primary-300">Homepage Builder</p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-gray-950 dark:text-white">Kelola alur cerita halaman beranda</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600 dark:text-gray-300">Gunakan tab untuk berpindah antar blok. Konten utama berada di depan, koleksi Zoom Moments dibuat seperti kartu, dan section lanjutan disimpan rapi agar halaman tidak terasa seperti form raksasa.</p>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center text-xs font-bold text-gray-600 dark:text-gray-300">
                        <div class="rounded-xl border border-gray-200 bg-white/70 px-3 py-2 dark:border-white/10 dark:bg-white/5"><span class="block text-lg text-primary-700 dark:text-primary-300">1</span>Hero</div>
                        <div class="rounded-xl border border-gray-200 bg-white/70 px-3 py-2 dark:border-white/10 dark:bg-white/5"><span class="block text-lg text-primary-700 dark:text-primary-300">2</span>Moving</div>
                        <div class="rounded-xl border border-gray-200 bg-white/70 px-3 py-2 dark:border-white/10 dark:bg-white/5"><span class="block text-lg text-primary-700 dark:text-primary-300">3</span>Zoom</div>
                    </div>
                </div>
            </div>
        HTML);
    }

    private function imagePreviewCard(?string $imageUrl, string $title, string $emptyText): HtmlString
    {
        $imageUrl = trim((string) $imageUrl);
        $title = e($title);
        $emptyText = e($emptyText);

        if ($imageUrl === '') {
            return new HtmlString(<<<HTML
                <div class="grid min-h-52 place-items-center rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center dark:border-white/15 dark:bg-white/5">
                    <div>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-200">{$title}</p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{$emptyText}</p>
                    </div>
                </div>
            HTML);
        }

        $escapedUrl = e($imageUrl);

        return new HtmlString(<<<HTML
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-white/5">
                <div class="relative aspect-[16/10] bg-gray-100 dark:bg-black/30">
                    <img src="{$escapedUrl}" alt="{$title}" class="h-full w-full object-cover" loading="lazy" />
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                        <p class="text-sm font-bold text-white">{$title}</p>
                    </div>
                </div>
                <p class="truncate px-3 py-2 text-xs text-gray-500 dark:text-gray-400" title="{$escapedUrl}">{$escapedUrl}</p>
            </div>
        HTML);
    }

    private function copyPreview(?string $headline, ?string $eyebrow = null): HtmlString
    {
        $headline = e($headline ?: 'Belum ada judul');
        $eyebrow = e($eyebrow ?: 'Preview');

        return new HtmlString(<<<HTML
            <div class="rounded-xl border border-gray-200 bg-white/75 p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
                <p class="text-[11px] font-black uppercase tracking-[0.2em] text-primary-700 dark:text-primary-300">{$eyebrow}</p>
                <p class="mt-2 text-xl font-black leading-tight text-gray-950 dark:text-white">{$headline}</p>
            </div>
        HTML);
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function zoomMomentCardPreview(array $state): HtmlString
    {
        $title = e((string) (($state['title'] ?? null) ?: 'Zoom moment baru'));
        $description = e((string) (($state['description'] ?? null) ?: 'Lengkapi deskripsi momen agar admin lain paham cerita yang ingin ditampilkan.'));
        $order = e((string) (($state['display_order'] ?? null) ?: '-'));
        $isActive = (bool) ($state['is_active'] ?? true);
        $statusLabel = $isActive ? 'Aktif' : 'Nonaktif';
        $statusClass = $isActive
            ? 'bg-emerald-500 text-white'
            : 'bg-gray-700 text-white';
        $outImage = trim((string) ($state['zoom_out_image_url'] ?? ''));
        $inImage = trim((string) ($state['zoom_in_image_url'] ?? ''));
        $image = $outImage !== '' ? $outImage : $inImage;
        $imageLabel = $outImage !== '' ? 'Zoom-out image' : 'Zoom-in image';
        $thumb = $image !== ''
            ? '<img src="' . e($image) . '" alt="" class="h-full w-full object-cover" loading="lazy" />'
            : '<div class="grid h-full place-items-center bg-gradient-to-br from-gray-100 to-gray-200 text-sm font-bold text-gray-400 dark:from-white/5 dark:to-white/10 dark:text-gray-500">Belum ada gambar</div>';
        $outBadge = $outImage !== '' ? 'Ada zoom-out' : 'Zoom-out kosong';
        $inBadge = $inImage !== '' ? 'Ada zoom-in' : 'Zoom-in kosong';

        return new HtmlString(<<<HTML
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-white/5">
                <div class="relative aspect-[16/8.5] min-h-52 overflow-hidden bg-gray-100 dark:bg-black/30">
                    {$thumb}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/78 via-black/18 to-transparent"></div>
                    <div class="absolute left-4 right-4 top-4 flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-amber-400 px-3 py-1 text-[11px] font-black uppercase tracking-wide text-amber-950">Order {$order}</span>
                        <span class="rounded-full px-3 py-1 text-[11px] font-black uppercase tracking-wide {$statusClass}">{$statusLabel}</span>
                        <span class="rounded-full bg-white/85 px-3 py-1 text-[11px] font-black uppercase tracking-wide text-gray-800">{$imageLabel}</span>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <p class="text-2xl font-black leading-tight text-white md:text-3xl">{$title}</p>
                        <p class="mt-2 max-w-2xl line-clamp-2 text-sm leading-5 text-white/82">{$description}</p>
                    </div>
                </div>
                <div class="grid gap-2 border-t border-gray-100 p-3 text-xs font-bold text-gray-600 dark:border-white/10 dark:text-gray-300 sm:grid-cols-3">
                    <div class="rounded-xl bg-gray-50 px-3 py-2 dark:bg-white/5">{$outBadge}</div>
                    <div class="rounded-xl bg-gray-50 px-3 py-2 dark:bg-white/5">{$inBadge}</div>
                    <div class="rounded-xl bg-gray-50 px-3 py-2 dark:bg-white/5">Buka kartu untuk edit detail</div>
                </div>
            </div>
        HTML);
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function horizontalCardPreview(array $state): HtmlString
    {
        $title = e((string) ($state['title'] ?? 'Story card baru'));
        $description = e((string) ($state['description'] ?? 'Lengkapi deskripsi kartu.'));
        $order = e((string) ($state['display_order'] ?? '-'));
        $link = e((string) ($state['link_url'] ?? 'Tanpa link'));
        $isActive = (bool) ($state['is_active'] ?? true);
        $statusLabel = $isActive ? 'Aktif' : 'Nonaktif';
        $image = trim((string) ($state['image_url'] ?? ''));
        $thumb = $image !== ''
            ? '<img src="' . e($image) . '" alt="" class="h-full w-full object-cover" loading="lazy" />'
            : '<div class="grid h-full place-items-center bg-gray-100 text-xs font-bold text-gray-400 dark:bg-white/5 dark:text-gray-500">No image</div>';

        return new HtmlString(<<<HTML
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-white/5">
                <div class="flex gap-3 p-3">
                    <div class="h-24 w-28 flex-none overflow-hidden rounded-lg bg-gray-100 dark:bg-black/30">{$thumb}</div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-amber-100 px-2 py-1 text-[11px] font-black text-amber-800 dark:bg-amber-500/15 dark:text-amber-200">Order {$order}</span>
                            <span class="rounded-full bg-primary-100 px-2 py-1 text-[11px] font-black text-primary-800 dark:bg-primary-500/15 dark:text-primary-200">{$statusLabel}</span>
                        </div>
                        <p class="mt-2 truncate text-base font-black text-gray-950 dark:text-white">{$title}</p>
                        <p class="mt-1 line-clamp-2 text-sm leading-5 text-gray-600 dark:text-gray-300">{$description}</p>
                        <p class="mt-2 truncate text-xs font-semibold text-gray-400 dark:text-gray-500">{$link}</p>
                    </div>
                </div>
            </div>
        HTML);
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function zoomItemLabel(array $state): string
    {
        $title = trim((string) ($state['title'] ?? 'Zoom moment'));
        $status = ($state['is_active'] ?? true) ? 'Aktif' : 'Nonaktif';
        $order = $state['display_order'] ?? '-';

        return "#{$order} {$title} - {$status}";
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function horizontalItemLabel(array $state): string
    {
        $title = trim((string) ($state['title'] ?? 'Story card'));
        $status = ($state['is_active'] ?? true) ? 'Aktif' : 'Nonaktif';
        $order = $state['display_order'] ?? '-';

        return "#{$order} {$title} - {$status}";
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
