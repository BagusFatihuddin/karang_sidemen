<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Pages\BookingVerificationPage;
use App\Filament\Admin\Pages\ReportsPage;
use App\Filament\Admin\Pages\SettingsPage;
use App\Filament\Admin\Pages\VisitorRegistrationPage;
use App\Filament\Admin\Resources\Destinations\DestinationResource;
use App\Filament\Admin\Resources\Guides\GuideResource;
use App\Filament\Admin\Resources\Promos\PromoResource;
use App\Filament\Admin\Resources\Reviews\ReviewResource;
use App\Filament\Admin\Resources\TripPackages\TripPackageResource;
use App\Support\UserRole;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class QuickActionsWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.quick-actions-widget';

    protected static ?int $sort = -3;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'actions' => $this->actions(),
        ];
    }

    private function actions(): array
    {
        return match (Auth::user()?->role) {
            UserRole::PETUGAS_LAPANGAN => [
                [
                    'label' => 'Registrasi Wisatawan',
                    'description' => 'Catat pengunjung yang baru datang.',
                    'url' => VisitorRegistrationPage::getUrl(),
                    'tone' => 'success',
                ],
                [
                    'label' => 'Verifikasi Booking',
                    'description' => 'Cek kode booking saat check-in.',
                    'url' => BookingVerificationPage::getUrl(),
                    'tone' => 'warning',
                ],
            ],
            UserRole::SUPER_ADMIN => [
                [
                    'label' => 'Tambah Destinasi',
                    'description' => 'Isi spot baru, story, lokasi, dan galeri.',
                    'url' => DestinationResource::getUrl('create'),
                    'tone' => 'success',
                ],
                [
                    'label' => 'Tambah Paket',
                    'description' => 'Rangkai destinasi menjadi pengalaman.',
                    'url' => TripPackageResource::getUrl('create'),
                    'tone' => 'info',
                ],
                [
                    'label' => 'Tambah Event',
                    'description' => 'Publikasikan agenda atau kegiatan POKDARWIS.',
                    'url' => PromoResource::getUrl('create'),
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Tambah Guide',
                    'description' => 'Lengkapi profil pendamping lokal.',
                    'url' => GuideResource::getUrl('create'),
                    'tone' => 'success',
                ],
                [
                    'label' => 'Moderasi Review',
                    'description' => 'Approve dan pin review pengunjung.',
                    'url' => ReviewResource::getUrl(),
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Website Media',
                    'description' => 'Atur homepage, media halaman, brand, dan footer.',
                    'url' => SettingsPage::getUrl(),
                    'tone' => 'gray',
                ],
                [
                    'label' => 'Laporan',
                    'description' => 'Lihat dan export data kunjungan.',
                    'url' => ReportsPage::getUrl(),
                    'tone' => 'info',
                ],
            ],
            UserRole::ADMIN_KONTEN => [
                [
                    'label' => 'Tambah Destinasi',
                    'description' => 'Isi spot baru, story, lokasi, dan galeri.',
                    'url' => DestinationResource::getUrl('create'),
                    'tone' => 'success',
                ],
                [
                    'label' => 'Tambah Paket',
                    'description' => 'Rangkai destinasi menjadi pengalaman.',
                    'url' => TripPackageResource::getUrl('create'),
                    'tone' => 'info',
                ],
                [
                    'label' => 'Tambah Event',
                    'description' => 'Publikasikan agenda atau kegiatan POKDARWIS.',
                    'url' => PromoResource::getUrl('create'),
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Tambah Guide',
                    'description' => 'Lengkapi profil pendamping lokal.',
                    'url' => GuideResource::getUrl('create'),
                    'tone' => 'success',
                ],
                [
                    'label' => 'Moderasi Review',
                    'description' => 'Approve dan pin review pengunjung.',
                    'url' => ReviewResource::getUrl(),
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Website Media',
                    'description' => 'Atur homepage, media halaman, brand, dan footer.',
                    'url' => SettingsPage::getUrl(),
                    'tone' => 'gray',
                ],
                [
                    'label' => 'Laporan',
                    'description' => 'Lihat dan export data kunjungan.',
                    'url' => ReportsPage::getUrl(),
                    'tone' => 'info',
                ],
            ],
            default => [
                [
                    'label' => 'Lihat Laporan',
                    'description' => 'Pantau ringkasan kunjungan wisata.',
                    'url' => ReportsPage::getUrl(),
                    'tone' => 'info',
                ],
            ],
        };
    }
}
