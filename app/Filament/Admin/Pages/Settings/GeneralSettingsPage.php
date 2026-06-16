<?php

namespace App\Filament\Admin\Pages\Settings;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class GeneralSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $title = 'General Settings';

    protected static ?string $slug = 'settings/general';

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
            Section::make('Identitas Dasar Website')
                ->description('Nama, tagline, WhatsApp utama, dan URL frontend publik.')
                ->schema([
                    TextInput::make('village_name')
                        ->label('Nama Website / Desa Wisata')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('tagline')
                        ->label('Tagline')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('global_whatsapp')
                        ->label('WhatsApp Utama')
                        ->required()
                        ->tel()
                        ->rule('regex:/^(\+62|62|08)[0-9]{8,13}$/')
                        ->helperText('Dipakai untuk CTA umum dan tombol WhatsApp melayang.'),

                    TextInput::make('public_frontend_url')
                        ->label('Public Frontend URL')
                        ->helperText('Dipakai untuk membuat link review publik dari WhatsApp. Contoh: http://localhost:5173')
                        ->url()
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(2),
        ];
    }
}
