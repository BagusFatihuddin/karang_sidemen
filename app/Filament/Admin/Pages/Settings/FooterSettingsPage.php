<?php

namespace App\Filament\Admin\Pages\Settings;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class FooterSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $title = 'Footer Settings';

    protected static ?string $slug = 'settings/footer';

    protected static function settingKeys(): array
    {
        return [
            'media_footer_cta_image_url',
        ];
    }

    protected function schema(): array
    {
        return [
            Section::make('Footer CTA Image')
                ->description('Gambar visual besar di bagian footer halaman publik.')
                ->schema([
                    TextInput::make('media_footer_cta_image_url')
                        ->label('Footer CTA Image URL')
                        ->url()
                        ->maxLength(2048),
                    FileUpload::make($this->uploadFieldName('media_footer_cta_image_url'))
                        ->label('Upload Footer CTA Image')
                        ->image()
                        ->disk('local')
                        ->directory('tmp/settings-media')
                        ->visibility('private')
                        ->imageEditor(false)
                        ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                        ->maxSize(4096)
                        ->dehydrated(false),
                ])
                ->columns(2),
        ];
    }

    protected function beforeSave(array &$data): void
    {
        $this->handleSingleImageUpload($data, 'media_footer_cta_image_url');
    }
}
