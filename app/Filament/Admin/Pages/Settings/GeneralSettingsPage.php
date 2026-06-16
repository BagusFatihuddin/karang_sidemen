<?php

namespace App\Filament\Admin\Pages\Settings;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class GeneralSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $title = 'Identitas Dasar Website';

    protected static ?string $slug = 'settings/general';

    public function getTitle(): string
    {
        return '🏘️ Identitas Dasar Website';
    }

    public function getSubheading(): ?string
    {
        return 'Atur nama website, tagline, nomor WhatsApp utama, dan alamat website publik. Informasi ini akan ditampilkan di seluruh halaman.';
    }

    protected static function settingKeys(): array
    {
        return [
            'village_name',
            'tagline',
            'global_whatsapp',
            'public_frontend_url',
        ];
    }

    protected function schema(): array
    {
        return [
            Section::make('🌟 Nama & Tagline')
                ->description('Nama yang akan muncul di header dan dokumentasi website. Tagline adalah kalimat singkat yang menggambarkan desa wisata.')
                ->icon('heroicon-m-sparkles')
                ->schema([
                    TextInput::make('village_name')
                        ->label('Nama Desa Wisata')
                        ->placeholder('Contoh: Karang Sidemen')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Nama akan muncul di banner atas website dan email.'),

                    TextInput::make('tagline')
                        ->label('Tagline / Motto')
                        ->placeholder('Contoh: Keindahan Alam, Kehangatan Lokal')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Kalimat singkat yang mewakili desa wisata.'),
                ])
                ->columns(2),

            Section::make('📞 Kontak Utama')
                ->description('Nomor WhatsApp yang akan digunakan untuk semua tombol chat dan kontak dari pengunjung.')
                ->icon('heroicon-m-phone')
                ->schema([
                    TextInput::make('global_whatsapp')
                        ->label('Nomor WhatsApp Utama')
                        ->placeholder('Contoh: 62812345678 atau 081234567890')
                        ->required()
                        ->tel()
                        ->helperText('Gunakan format: +62, 62, atau 08. Nomor ini untuk semua tombol WhatsApp di website.')
                        ->hint('Format: 62812345678'),
                ])
                ->columns(1),

            Section::make('🌐 Alamat Website Publik')
                ->description('Alamat website yang dilihat pengunjung. Digunakan untuk membuat link review dan berbagi media sosial.')
                ->icon('heroicon-m-globe-alt')
                ->schema([
                    TextInput::make('public_frontend_url')
                        ->label('URL Website Publik')
                        ->placeholder('Contoh: https://www.karangsidemen.com')
                        ->url()
                        ->required()
                        ->maxLength(255)
                        ->helperText('Pastikan sudah benar! URL ini digunakan untuk link yang dibagikan ke pengunjung.'),
                ])
                ->columns(1),
        ];
    }
}
