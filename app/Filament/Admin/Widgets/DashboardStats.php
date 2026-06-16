<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Bookings\BookingResource;
use App\Filament\Admin\Resources\Destinations\DestinationResource;
use App\Filament\Admin\Resources\Reviews\ReviewResource;
use App\Filament\Admin\Resources\TripPackages\TripPackageResource;
use App\Models\Booking;
use App\Models\DailyVisit;
use App\Models\Destination;
use App\Models\Review;
use App\Models\TripPackage;
use App\Models\Visitor;
use App\Support\UserRole;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '60s';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->role !== UserRole::PETUGAS_LAPANGAN;
    }

    protected function getStats(): array
    {
        $role = Auth::user()?->role;

        $dashboardStats = Cache::remember(
            'admin:dashboard-stats:' . ($role ?? 'guest') . ':' . now()->format('Y-m-d-H-i'),
            now()->addSeconds(75),
            fn (): array => $this->resolveDashboardStats()
        );

        $stats = [
            Stat::make(
                'Pengunjung Hari Ini',
                number_format($dashboardStats['today_visitors'], 0, ',', '.')
            )
                ->description('Bulan ini: ' . $dashboardStats['month_visitors'] . ' pengunjung')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success')
                ->icon('heroicon-o-user-group')
                ->chart([$dashboardStats['today_visitors']])
                ->extraAttributes([
                    'class' =>
                        'cursor-pointer rounded-[24px] border border-emerald-600/30 bg-gradient-to-br from-emerald-950/60 via-emerald-900/30 to-black/40 shadow-xl shadow-emerald-900/20 transition duration-300 ease-out hover:-translate-y-2 hover:border-emerald-500/50 hover:shadow-2xl hover:shadow-emerald-900/40',
                ]),
        ];

        if (
            in_array(
                $role,
                [
                    UserRole::SUPER_ADMIN,
                    UserRole::ADMIN_KONTEN,
                ],
                true
            )
        ) {
            $pendingBookings = $dashboardStats['pending_bookings'];
            $pendingReviews = $dashboardStats['pending_reviews'];
            $activeDestinations = $dashboardStats['active_destinations'];
            $destinationsWithoutImages = $dashboardStats['destinations_without_images'];
            $activePackages = $dashboardStats['active_packages'];

            $stats[] = Stat::make(
                'Booking Pending',
                number_format($pendingBookings, 0, ',', '.')
            )
                ->description(
                    $pendingBookings > 0
                        ? 'Menunggu konfirmasi'
                        : 'Semua terkonfirmasi'
                )
                ->descriptionIcon(
                    $pendingBookings > 0
                        ? 'heroicon-m-exclamation-circle'
                        : 'heroicon-m-check-circle'
                )
                ->color($pendingBookings > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-clipboard-document-list')
                ->url(BookingResource::getUrl())
                ->extraAttributes([
                    'class' =>
                        'cursor-pointer rounded-[24px] border border-amber-600/30 bg-gradient-to-br from-amber-950/50 via-amber-900/20 to-black/40 shadow-xl shadow-amber-900/20 transition duration-300 ease-out hover:-translate-y-2 hover:border-amber-500/50 hover:shadow-2xl hover:shadow-amber-900/40',
                ]);

            $stats[] = Stat::make(
                'Review Pending',
                number_format($pendingReviews, 0, ',', '.')
            )
                ->description(
                    $pendingReviews > 0
                        ? 'Tunggu persetujuan'
                        : 'Semua disetujui'
                )
                ->descriptionIcon(
                    $pendingReviews > 0
                        ? 'heroicon-m-exclamation-circle'
                        : 'heroicon-m-check-circle'
                )
                ->color($pendingReviews > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-star')
                ->url(ReviewResource::getUrl())
                ->extraAttributes([
                    'class' =>
                        'cursor-pointer rounded-[24px] border border-rose-600/30 bg-gradient-to-br from-rose-950/50 via-rose-900/20 to-black/40 shadow-xl shadow-rose-900/20 transition duration-300 ease-out hover:-translate-y-2 hover:border-rose-500/50 hover:shadow-2xl hover:shadow-rose-900/40',
                ]);

            $stats[] = Stat::make(
                'Destinasi Aktif',
                number_format($activeDestinations, 0, ',', '.')
            )
                ->description(
                    $destinationsWithoutImages > 0
                        ? "{$destinationsWithoutImages} masih perlu media"
                        : 'Semua memiliki media'
                )
                ->descriptionIcon(
                    $destinationsWithoutImages > 0
                        ? 'heroicon-m-exclamation-triangle'
                        : 'heroicon-m-check-circle'
                )
                ->color($destinationsWithoutImages > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-map-pin')
                ->url(DestinationResource::getUrl())
                ->extraAttributes([
                    'class' =>
                        'cursor-pointer rounded-[24px] border border-sky-600/30 bg-gradient-to-br from-sky-950/50 via-sky-900/20 to-black/40 shadow-xl shadow-sky-900/20 transition duration-300 ease-out hover:-translate-y-2 hover:border-sky-500/50 hover:shadow-2xl hover:shadow-sky-900/40',
                ]);

            $stats[] = Stat::make(
                'Paket Aktif',
                number_format($activePackages, 0, ',', '.')
            )
                ->description(
                    $activePackages > 0
                        ? 'Siap tersedia untuk wisatawan'
                        : 'Tidak ada paket aktif'
                )
                ->descriptionIcon(
                    $activePackages > 0
                        ? 'heroicon-m-check-circle'
                        : 'heroicon-m-exclamation-circle'
                )
                ->color($activePackages > 0 ? 'success' : 'warning')
                ->icon('heroicon-o-map')
                ->url(TripPackageResource::getUrl())
                ->extraAttributes([
                    'class' =>
                        'cursor-pointer rounded-[24px] border border-indigo-600/30 bg-gradient-to-br from-indigo-950/50 via-indigo-900/20 to-black/40 shadow-xl shadow-indigo-900/20 transition duration-300 ease-out hover:-translate-y-2 hover:border-indigo-500/50 hover:shadow-2xl hover:shadow-indigo-900/40',
                ]);
        }

        $stats[] = Stat::make(
            'Pendapatan Bulan Ini',
            'Rp ' . number_format(
                $dashboardStats['month_revenue'],
                0,
                ',',
                '.'
            )
        )
            ->description('Dari input kunjungan harian')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('info')
            ->icon('heroicon-o-banknotes')
            ->extraAttributes([
                'class' =>
                    'rounded-[24px] border border-cyan-600/30 bg-gradient-to-br from-cyan-950/50 via-cyan-900/20 to-black/40 shadow-xl shadow-cyan-900/20 transition duration-300 ease-out hover:-translate-y-2 hover:border-cyan-500/50 hover:shadow-2xl hover:shadow-cyan-900/40',
            ]);

        return $stats;
    }

    private function resolveDashboardStats(): array
    {
        return [
            'today_visitors' => Visitor::query()
                ->where('visited_at', '>=', today())
                ->count(),

            'month_visitors' => Visitor::query()
                ->whereBetween('visited_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ])
                ->count(),

            'month_revenue' => (float) DailyVisit::query()
                ->whereBetween('date', [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString(),
                ])
                ->sum('revenue'),

            'pending_bookings' => Booking::query()
                ->where('status', 'pending')
                ->count(),

            'pending_reviews' => Review::query()
                ->where('status', 'pending')
                ->count(),

            'active_destinations' => Destination::query()
                ->where('is_active', true)
                ->count(),

            'destinations_without_images' => Destination::query()
                ->where('is_active', true)
                ->whereDoesntHave('images')
                ->count(),

            'active_packages' => TripPackage::query()
                ->where('is_active', true)
                ->count(),
        ];
    }
}