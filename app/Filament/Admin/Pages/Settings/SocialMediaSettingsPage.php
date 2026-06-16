<?php

namespace App\Filament\Admin\Pages\Settings;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class SocialMediaSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShare;

    protected static ?string $title = 'Social Media Settings';

    protected static ?string $slug = 'settings/social-media';

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
            Section::make('Link Media Sosial')
                ->description('Kosongkan field jika akun belum tersedia.')
                ->schema([
                    TextInput::make('social_instagram')
                        ->label('Instagram URL')
                        ->url()
                        ->maxLength(2048),

                    TextInput::make('social_facebook')
                        ->label('Facebook URL')
                        ->url()
                        ->maxLength(2048),

                    TextInput::make('social_tiktok')
                        ->label('TikTok URL')
                        ->url()
                        ->maxLength(2048),
                ]),
        ];
    }
}
