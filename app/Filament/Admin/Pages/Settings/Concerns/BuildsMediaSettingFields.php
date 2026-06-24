<?php

namespace App\Filament\Admin\Pages\Settings\Concerns;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

trait BuildsMediaSettingFields
{
    /**
     * @param  array<string, array{label: string, helper: string}>  $settings
     * @return array<int, mixed>
     */
    protected function mediaSettingFields(array $settings): array
    {
        $fields = [];

        foreach ($settings as $key => $definition) {
            $fields[] = TextInput::make($key)
                ->label($definition['label'])
                ->helperText($definition['helper'])
                ->url()
                ->maxLength(2048)
                ->extraAttributes([
                    'data-image-field' => $key,
                    'autocomplete' => 'off',
                ]);

            $fields[] = FileUpload::make($this->uploadFieldName($key))
                ->label('Upload ' . $definition['label'])
                ->image()
                ->disk('local')
                ->directory('tmp/settings-media')
                ->visibility('private')
                ->imageEditor(false)
                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                ->maxSize(2048)
                ->validationMessages([
                    'mimetypes' => 'Tipe file tidak sesuai. Gunakan gambar JPG, PNG, atau WEBP.',
                    'max' => 'Ukuran gambar terlalu besar. Maksimal 2 MB.',
                ])
                ->helperText('Opsional. Upload baru akan mengganti URL di field sebelahnya.')
                ->dehydrated(false)
                ->extraAttributes([
                    'data-file-field' => $key,
                ]);
        }

        return $fields;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, array{label: string, helper: string}>  $settings
     */
    protected function handleMediaSettingUploads(array &$data, array $settings): void
    {
        foreach (array_keys($settings) as $key) {
            $this->handleSingleImageUpload($data, $key);
        }
    }
}
