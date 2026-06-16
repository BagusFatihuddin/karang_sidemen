<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Services\CloudinaryService;
use App\Support\AppSettings;
use App\Support\UserRole;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

abstract class BaseSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.admin.pages.settings.form-page';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $data = [];

        $settings = AppSettings::all();

        foreach (static::settingKeys() as $key) {
            $data[$key] = $settings[$key] ?? null;
        }

        $this->form->fill($data);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, static::allowedRoles(), true);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components($this->schema());
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->beforeSave($data);

        foreach ($data as $key => $value) {
            if (in_array($key, static::secretKeys(), true) && blank($value)) {
                continue;
            }

            AppSettings::set($key, $value);
        }

        AppSettings::clearCache();

        Notification::make()
            ->title('Pengaturan berhasil disimpan.')
            ->success()
            ->send();
    }

    /**
     * @return list<string>
     */
    protected static function allowedRoles(): array
    {
        return [
            UserRole::SUPER_ADMIN,
            UserRole::ADMIN_KONTEN,
        ];
    }

    /**
     * @return list<string>
     */
    abstract protected static function settingKeys(): array;

    /**
     * @return array<int, mixed>
     */
    abstract protected function schema(): array;

    /**
     * @return list<string>
     */
    protected static function secretKeys(): array
    {
        return [];
    }

    protected function beforeSave(array &$data): void
    {
        //
    }

    protected function uploadFieldName(string $settingKey): string
    {
        return $settingKey . '_upload';
    }

    protected function handleSingleImageUpload(
        array &$data,
        string $settingKey,
        string $folder = 'website-public-media'
    ): void {
        $uploadState = $this->data[$this->uploadFieldName($settingKey)] ?? null;
        $path = $this->extractUploadPath($uploadState);

        if (! $path) {
            return;
        }

        try {
            $fullPath = Storage::disk('local')->path($path);

            $uploadedFile = new UploadedFile(
                $fullPath,
                basename($fullPath),
                mime_content_type($fullPath) ?: 'image/jpeg',
                test: true
            );

            $result = app(CloudinaryService::class)->upload($uploadedFile, $folder);

            if (filled($result['url'] ?? null)) {
                $data[$settingKey] = $result['url'];
            }
        } catch (\Throwable $e) {
            Log::warning('Settings media upload failed', [
                'setting_key' => $settingKey,
                'message' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Upload gambar gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function extractUploadPath(mixed $uploadState): ?string
    {
        if (is_string($uploadState)) {
            return $uploadState;
        }

        if (is_array($uploadState)) {
            $path = reset($uploadState);

            return is_string($path) ? $path : null;
        }

        return null;
    }
}
