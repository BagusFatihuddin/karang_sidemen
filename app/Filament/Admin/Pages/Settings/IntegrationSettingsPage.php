<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Support\UserRole;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class IntegrationSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCloud;

    protected static ?string $title = 'Integrasi Teknis';

    protected static ?string $slug = 'settings/integrations';

    public function getTitle(): string
    {
        return '⚙️ Integrasi Sistem & API';
    }

    public function getSubheading(): ?string
    {
        return 'Pengaturan teknis untuk layanan pihak ketiga yang digunakan website (Cloudinary untuk upload gambar). Hanya admin teknis yang perlu mengakses halaman ini.';
    }

    protected static function allowedRoles(): array
    {
        return [
            UserRole::SUPER_ADMIN,
        ];
    }

    protected static function settingKeys(): array
    {
        return [
            'cloudinary_cloud_name',
            'cloudinary_api_key',
            'cloudinary_api_secret',
        ];
    }

    protected static function secretKeys(): array
    {
        return [
            'cloudinary_api_key',
            'cloudinary_api_secret',
        ];
    }

    protected function schema(): array
    {
        return [
            Section::make('☁️ Pengaturan Cloudinary')
                ->description('Cloudinary adalah layanan penyimpanan gambar online. Digunakan untuk mengunggah dan menyimpan semua gambar di website agar lebih cepat dan efisien. API Key dan Secret jangan dibagikan ke orang lain!')
                ->icon('heroicon-m-cloud')
                ->schema([
                    TextInput::make('cloudinary_cloud_name')
                        ->label('Cloud Name')
                        ->placeholder('Contoh: mycloud123')
                        ->helperText('Nama unik Cloudinary Anda. Bisa dilihat di dashboard Cloudinary.')
                        ->maxLength(255),

                    TextInput::make('cloudinary_api_key')
                        ->label('🔑 API Key')
                        ->password()
                        ->revealable()
                        ->placeholder('Gunakan placeholder untuk keamanan')
                        ->helperText('Kode akses Cloudinary. Jangan bagikan ke siapa pun! Jika kosong, nilai lama akan tetap digunakan.')
                        ->maxLength(255),

                    TextInput::make('cloudinary_api_secret')
                        ->label('🔐 API Secret')
                        ->password()
                        ->revealable()
                        ->placeholder('Gunakan placeholder untuk keamanan')
                        ->helperText('Kode rahasia Cloudinary. Jangan bagikan ke siapa pun! Jika kosong, nilai lama akan tetap digunakan.')
                        ->maxLength(255),
                ])
                ->columns(1),
        ];
    }
}
