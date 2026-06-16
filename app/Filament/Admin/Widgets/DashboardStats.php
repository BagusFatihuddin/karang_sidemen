<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Booking;
use App\Models\DailyVisit;
use App\Models\Destination;
use App\Models\Review;
use App\Models\TripPackage;
use App\Models\Visitor;
use App\Filament\Admin\Resources\Bookings\BookingResource;
use App\Filament\Admin\Resources\Destinations\DestinationResource;
use App\Filament\Admin\Resources\Reviews\ReviewResource;
use App\Filament\Admin\Resources\TripPackages\TripPackageResource;
use App\Support\UserRole;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardStats extends StatsOverviewWidget
{
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
                ->description($dashboardStats['month_visitors'] . ' pengunjung bulan ini')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->icon('heroicon-o-user-group'),
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
                ->description('Perlu dikonfirmasi admin')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingBookings > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-clipboard-document-list')
                ->url(BookingResource::getUrl());

            $stats[] = Stat::make(
                'Review Pending',
                number_format($pendingReviews, 0, ',', '.')
            )
                ->description('Approve agar tampil di website')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($pendingReviews > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-star')
                ->url(ReviewResource::getUrl());

            $stats[] = Stat::make(
                'Destinasi Aktif',
                number_format($activeDestinations, 0, ',', '.')
            )
                ->description(
                    $destinationsWithoutImages > 0
                        ? "{$destinationsWithoutImages} belum punya gambar"
                        : 'Semua punya media dasar'
                )
                ->descriptionIcon(
                    $destinationsWithoutImages > 0
                        ? 'heroicon-m-exclamation-triangle'
                        : 'heroicon-m-check-circle'
                )
                ->color($destinationsWithoutImages > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-map-pin')
                ->url(DestinationResource::getUrl());

            $stats[] = Stat::make(
                'Paket Aktif',
                number_format($activePackages, 0, ',', '.')
            )
                ->description(
                    $activePackages > 0
                        ? 'Siap ditampilkan di halaman publik'
                        : 'Belum ada paket aktif'
                )
                ->descriptionIcon(
                    $activePackages > 0
                        ? 'heroicon-m-check-circle'
                        : 'heroicon-m-plus-circle'
                )
                ->color($activePackages > 0 ? 'success' : 'warning')
                ->icon('heroicon-o-map')
                ->url(TripPackageResource::getUrl());
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
            ->description('Berdasarkan input harian')
            ->descriptionIcon('heroicon-m-banknotes')
            ->color('info')
            ->icon('heroicon-o-chart-bar');

        return $stats;
    }

    /**
     * @return array<string, int|float>
     */
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
                ->whereBetween(
                    'date',
                    [
                        now()->startOfMonth()->toDateString(),
                        now()->endOfMonth()->toDateString(),
                    ]
                )
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
