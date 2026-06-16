<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Pages\Settings\AboutPageSettingsPage;
use App\Filament\Admin\Pages\Settings\BrandSettingsPage;
use App\Filament\Admin\Pages\Settings\DestinationPageSettingsPage;
use App\Filament\Admin\Pages\Settings\FooterSettingsPage;
use App\Filament\Admin\Pages\Settings\GeneralSettingsPage;
use App\Filament\Admin\Pages\Settings\GuidesPageSettingsPage;
use App\Filament\Admin\Pages\Settings\HomepageSettingsPage;
use App\Filament\Admin\Pages\Settings\IntegrationSettingsPage;
use App\Filament\Admin\Pages\Settings\PackagesPageSettingsPage;
use App\Filament\Admin\Pages\Settings\ReviewsPageSettingsPage;
use App\Filament\Admin\Pages\Settings\SocialMediaSettingsPage;
use App\Support\UserRole;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class SettingsPage extends Page
{
    protected string $view = 'filament.admin.pages.settings-page';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Pengaturan Website';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $slug = 'settings';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, [
            UserRole::SUPER_ADMIN,
            UserRole::ADMIN_KONTEN,
        ], true);
    }

    /**
     * @return array<string, array<int, array{label: string, description: string, url: string, tone: string, icon: string}>>
     */
    public function cards(): array
    {
        return collect([
            'Dasar Website' => [
                [
                    'page' => GeneralSettingsPage::class,
                    'label' => 'Identitas Dasar',
                    'description' => 'Nama desa, tagline, WhatsApp utama.',
                    'tone' => 'emerald',
                    'icon' => 'heroicon-m-building-library',
                ],
                [
                    'page' => BrandSettingsPage::class,
                    'label' => 'Brand & Logo',
                    'description' => 'Logo, icon, dan identitas visual desa.',
                    'tone' => 'amber',
                    'icon' => 'heroicon-m-sparkles',
                ],
                [
                    'page' => SocialMediaSettingsPage::class,
                    'label' => 'Media Sosial',
                    'description' => 'Instagram, Facebook, TikTok desa wisata.',
                    'tone' => 'sky',
                    'icon' => 'heroicon-m-share',
                ],
            ],
            'Tampilan Website' => [
                [
                    'page' => HomepageSettingsPage::class,
                    'label' => 'Halaman Utama',
                    'description' => 'Gambar hero dan konten di halaman depan.',
                    'tone' => 'violet',
                    'icon' => 'heroicon-m-rectangle-stack',
                ],
                [
                    'page' => DestinationPageSettingsPage::class,
                    'label' => 'Halaman Destinasi',
                    'description' => 'Gambar dan tampilan daftar destinasi.',
                    'tone' => 'teal',
                    'icon' => 'heroicon-m-map-pin',
                ],
                [
                    'page' => PackagesPageSettingsPage::class,
                    'label' => 'Halaman Paket',
                    'description' => 'Gambar dan tampilan daftar paket wisata.',
                    'tone' => 'cyan',
                    'icon' => 'heroicon-m-shopping-cart',
                ],
                [
                    'page' => GuidesPageSettingsPage::class,
                    'label' => 'Halaman Panduan',
                    'description' => 'Gambar dan tampilan daftar guide lokal.',
                    'tone' => 'purple',
                    'icon' => 'heroicon-m-user-group',
                ],
                [
                    'page' => ReviewsPageSettingsPage::class,
                    'label' => 'Halaman Review',
                    'description' => 'Gambar dan tampilan review pengunjung.',
                    'tone' => 'rose',
                    'icon' => 'heroicon-m-star',
                ],
                [
                    'page' => AboutPageSettingsPage::class,
                    'label' => 'Halaman Tentang',
                    'description' => 'Cerita, peta, dan info tentang desa wisata.',
                    'tone' => 'lime',
                    'icon' => 'heroicon-m-information-circle',
                ],
            ],
            'Footer & Teknis' => [
                [
                    'page' => FooterSettingsPage::class,
                    'label' => 'Footer',
                    'description' => 'Gambar dan konten di bagian bawah website.',
                    'tone' => 'orange',
                    'icon' => 'heroicon-m-bars-3-bottom-left',
                ],
                [
                    'page' => IntegrationSettingsPage::class,
                    'label' => 'Integrasi Teknis',
                    'description' => 'Pengaturan upload gambar dan sistem eksternal.',
                    'tone' => 'gray',
                    'icon' => 'heroicon-m-cog-6-tooth',
                ],
            ],
        ])
            ->map(fn (array $cards): array => collect($cards)
                ->filter(fn (array $card): bool => $card['page']::canAccess())
                ->map(fn (array $card): array => [
                    'label' => $card['label'],
                    'description' => $card['description'],
                    'url' => $card['page']::getUrl(),
                    'tone' => $card['tone'],
                    'icon' => $card['icon'],
                ])
                ->values()
                ->all()
            )
            ->filter(fn (array $cards): bool => ! empty($cards))
            ->all();
    }
}
