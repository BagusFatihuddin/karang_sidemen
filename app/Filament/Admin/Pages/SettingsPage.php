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
     * @return array<int, array{label: string, description: string, url: string, tone: string}>
     */
    public function cards(): array
    {
        return collect([
            [
                'page' => GeneralSettingsPage::class,
                'label' => 'General Settings',
                'description' => 'Nama desa, tagline, WhatsApp utama, dan URL frontend publik.',
                'tone' => 'emerald',
            ],
            [
                'page' => BrandSettingsPage::class,
                'label' => 'Brand / Logo',
                'description' => 'Atur icon KS, logo, dan identitas visual navbar serta footer.',
                'tone' => 'amber',
            ],
            [
                'page' => SocialMediaSettingsPage::class,
                'label' => 'Social Media',
                'description' => 'Link Instagram, Facebook, dan TikTok publik.',
                'tone' => 'sky',
            ],
            [
                'page' => HomepageSettingsPage::class,
                'label' => 'Homepage Cinematic',
                'description' => 'Hero image dan copy homepage per section: hero, reel, portal, horizontal, review, dan final CTA.',
                'tone' => 'violet',
            ],
            [
                'page' => DestinationPageSettingsPage::class,
                'label' => 'Halaman Destinasi',
                'description' => 'Hero image khusus halaman /destinasi.',
                'tone' => 'teal',
            ],
            [
                'page' => PackagesPageSettingsPage::class,
                'label' => 'Halaman Paket',
                'description' => 'Hero, empty state, dan fallback gambar kartu paket.',
                'tone' => 'emerald',
            ],
            [
                'page' => GuidesPageSettingsPage::class,
                'label' => 'Halaman Panduan',
                'description' => 'Hero, empty state, dan gambar card kenapa pakai guide.',
                'tone' => 'amber',
            ],
            [
                'page' => ReviewsPageSettingsPage::class,
                'label' => 'Halaman Review',
                'description' => 'Hero image khusus halaman /reviews.',
                'tone' => 'sky',
            ],
            [
                'page' => AboutPageSettingsPage::class,
                'label' => 'Halaman Tentang',
                'description' => 'Hero, gambar cerita lokal, peta, dan struktur organisasi opsional.',
                'tone' => 'lime',
            ],
            [
                'page' => FooterSettingsPage::class,
                'label' => 'Footer',
                'description' => 'Gambar footer CTA. Identitas pengembang dikunci dari kode.',
                'tone' => 'rose',
            ],
            [
                'page' => IntegrationSettingsPage::class,
                'label' => 'Integrasi Sistem',
                'description' => 'Cloudinary dan pengaturan teknis upload gambar.',
                'tone' => 'gray',
            ],
        ])
            ->filter(fn (array $card): bool => $card['page']::canAccess())
            ->map(fn (array $card): array => [
                'label' => $card['label'],
                'description' => $card['description'],
                'url' => $card['page']::getUrl(),
                'tone' => $card['tone'],
            ])
            ->values()
            ->all();
    }
}
