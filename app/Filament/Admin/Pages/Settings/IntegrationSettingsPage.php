<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Support\UserRole;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class IntegrationSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCloud;

    protected static ?string $title = 'Integration Settings';

    protected static ?string $slug = 'settings/integrations';

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
            Section::make('Cloudinary')
                ->description('Dipakai untuk upload gambar dari admin. API Key dan Secret kosong tidak akan menimpa nilai lama.')
                ->schema([
                    TextInput::make('cloudinary_cloud_name')
                        ->label('Cloud Name')
                        ->maxLength(255),

                    TextInput::make('cloudinary_api_key')
                        ->label('API Key')
                        ->password()
                        ->revealable(),

                    TextInput::make('cloudinary_api_secret')
                        ->label('API Secret')
                        ->password()
                        ->revealable(),
                ]),
        ];
    }
}
