<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Booking;
use App\Models\DailyVisit;
use App\Models\Review;
use App\Models\Visitor;
use App\Support\UserRole;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardStats extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return Auth::user()?->role !== UserRole::PETUGAS_LAPANGAN;
    }

    protected function getStats(): array
    {
        $role = Auth::user()?->role;

        $stats = [
            Stat::make(
                'Pengunjung Hari Ini',
                Visitor::query()
                    ->where('visited_at', '>=', today())
                    ->count()
            ),
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
            $stats[] = Stat::make(
                'Booking Pending',
                Booking::query()
                    ->where('status', 'pending')
                    ->count()
            );

            $stats[] = Stat::make(
                'Review Pending',
                Review::query()
                    ->where('status', 'pending')
                    ->count()
            );
        }

        $stats[] = Stat::make(
            'Pendapatan Bulan Ini',
            'Rp ' . number_format(
                (float) DailyVisit::query()
                    ->whereBetween(
                        'date',
                        [
                            now()->startOfMonth()->toDateString(),
                            now()->endOfMonth()->toDateString(),
                        ]
                    )
                    ->sum('revenue'),
                0,
                ',',
                '.'
            )
        );

        return $stats;
    }
}
