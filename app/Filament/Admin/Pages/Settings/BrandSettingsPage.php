<?php

namespace App\Filament\Admin\Pages\Settings;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class BrandSettingsPage extends BaseSettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $title = 'Brand Settings';

    protected static ?string $slug = 'settings/brand';

    protected static function settingKeys(): array
    {
        return [
            'brand_mark_text',
            'brand_logo_url',
            'brand_logo_alt',
        ];
    }

    protected function schema(): array
    {
        return [
            Section::make('Logo dan Identitas Visual')
                ->description('Kontrol icon KS/logo yang muncul di navbar dan footer publik.')
                ->schema([
                    TextInput::make('brand_mark_text')
                        ->label('Teks Icon Jika Logo Kosong')
                        ->helperText('Contoh: KS. Akan dipakai sebagai fallback kalau logo belum di-upload.')
                        ->maxLength(12),

                    TextInput::make('brand_logo_alt')
                        ->label('Alt Text Logo')
                        ->helperText('Deskripsi singkat untuk aksesibilitas.')
                        ->maxLength(255),

                    TextInput::make('brand_logo_url')
                        ->label('Logo URL')
                        ->url()
                        ->maxLength(2048),

                    FileUpload::make($this->uploadFieldName('brand_logo_url'))
                        ->label('Upload Logo')
                        ->image()
                        ->disk('local')
                        ->directory('tmp/settings-media')
                        ->visibility('private')
                        ->imageEditor(false)
                        ->acceptedFileTypes([
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/webp',
                            'image/svg+xml',
                        ])
                        ->maxSize(2048)
                        ->helperText('Opsional. Upload baru akan mengganti Logo URL.')
                        ->dehydrated(false),
                ])
                ->columns(2),
        ];
    }

    protected function beforeSave(array &$data): void
    {
        $this->handleSingleImageUpload($data, 'brand_logo_url', 'website-brand');
    }
}
