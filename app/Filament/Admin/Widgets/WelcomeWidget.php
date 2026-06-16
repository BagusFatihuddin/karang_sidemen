<?php

namespace App\Filament\Admin\Widgets;

use App\Support\UserRole;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.welcome-widget';

    protected static ?int $sort = -4;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = Auth::user();

        return [
            'name' => $user?->name ?? 'Admin',
            'role' => $this->resolveRole($user?->role),
            'greeting' => $this->resolveGreeting(),
            'date' => now()->translatedFormat('l, d F Y'),
        ];
    }

    private function resolveGreeting(): string
    {
        $hour = now()->hour;

        return match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }

    private function resolveRole(?string $role): string
    {
        return match ($role) {
            UserRole::SUPER_ADMIN => 'Super Admin',
            UserRole::ADMIN_KONTEN => 'Admin Konten',
            UserRole::PETUGAS_LAPANGAN => 'Petugas Lapangan',
            default => 'Administrator',
        };
    }
}