<?php

namespace App\Filament\Admin\Pages\Settings;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class SocialMediaSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShare;

    protected static ?string $title = 'Media Sosial';

    protected static ?string $slug = 'settings/social-media';

    public function getTitle(): string
    {
        return '📱 Media Sosial Desa Wisata';
    }

    public function getSubheading(): ?string
    {
        return 'Masukkan link profil media sosial desa wisata. Jika belum memiliki akun, kosongkan field tersebut. Link ini akan ditampilkan di halaman website sehingga pengunjung dapat mengikuti dan membagikan konten Anda.';
    }

    protected static function settingKeys(): array
    {
        return [
            'social_instagram',
            'social_facebook',
            'social_tiktok',
        ];
    }

    protected function schema(): array
    {
        return [
            Section::make('👥 Akun Media Sosial')
                ->description('Salin dan tempel URL profil media sosial lengkap dari platform masing-masing.')
                ->icon('heroicon-m-share')
                ->schema([
                    TextInput::make('social_instagram')
                        ->label('📸 Link Instagram')
                        ->placeholder('Contoh: https://instagram.com/karangsidemen')
                        ->url()
                        ->maxLength(2048)
                        ->helperText('Profil Instagram akan muncul di footer website. Kosongkan jika belum ada akun.'),

                    TextInput::make('social_facebook')
                        ->label('👍 Link Facebook')
                        ->placeholder('Contoh: https://facebook.com/karangsidemen')
                        ->url()
                        ->maxLength(2048)
                        ->helperText('Profil Facebook akan muncul di footer website. Kosongkan jika belum ada akun.'),

                    TextInput::make('social_tiktok')
                        ->label('🎵 Link TikTok')
                        ->placeholder('Contoh: https://tiktok.com/@karangsidemen')
                        ->url()
                        ->maxLength(2048)
                        ->helperText('Profil TikTok akan muncul di footer website. Kosongkan jika belum ada akun.'),
                ])
                ->columns(1),
        ];
    }
}
