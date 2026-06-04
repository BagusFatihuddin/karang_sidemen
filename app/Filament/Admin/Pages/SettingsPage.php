<?php

namespace App\Filament\Admin\Pages;

use App\Support\AppSettings;
use App\Support\UserRole;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class SettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.admin.pages.settings-page';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'System Settings';

    protected static ?string $slug = 'settings';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->role === UserRole::SUPER_ADMIN,
            403
        );

        $this->form->fill([
            'village_name' => AppSettings::get('village_name'),
            'tagline' => AppSettings::get('tagline'),
            'global_whatsapp' => AppSettings::get('global_whatsapp'),

            'social_instagram' => AppSettings::get('social_instagram'),
            'social_facebook' => AppSettings::get('social_facebook'),
            'social_tiktok' => AppSettings::get('social_tiktok'),

            'google_maps_embed_url' => AppSettings::get('google_maps_embed_url'),

            'cloudinary_cloud_name' => AppSettings::get('cloudinary_cloud_name'),
            'cloudinary_api_key' => AppSettings::get('cloudinary_api_key'),
            'cloudinary_api_secret' => AppSettings::get('cloudinary_api_secret'),
        ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role === UserRole::SUPER_ADMIN;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->role === UserRole::SUPER_ADMIN;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('General Settings')
                    ->schema([
                        TextInput::make('village_name')
                            ->label('Village Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('tagline')
                            ->label('Tagline')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('global_whatsapp')
                            ->label('Global WhatsApp')
                            ->required()
                            ->tel()
                            ->rule('regex:/^(\+62|62|08)[0-9]{8,13}$/'),
                    ]),

                Section::make('Social Media')
                    ->schema([
                        TextInput::make('social_instagram')
                            ->label('Instagram URL')
                            ->url(),

                        TextInput::make('social_facebook')
                            ->label('Facebook URL')
                            ->url(),

                        TextInput::make('social_tiktok')
                            ->label('TikTok URL')
                            ->url(),
                    ]),

                Section::make('Google Maps')
                    ->schema([
                        Textarea::make('google_maps_embed_url')
                            ->label('Google Maps Embed URL')
                            ->rows(3),
                    ]),

                Section::make('Cloudinary')
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
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if (
                in_array($key, [
                    'cloudinary_api_key',
                    'cloudinary_api_secret',
                ], true)
                && blank($value)
            ) {
                continue;
            }

            AppSettings::set($key, $value);
        }

        Notification::make()
            ->title('Settings saved successfully.')
            ->success()
            ->send();
    }
}