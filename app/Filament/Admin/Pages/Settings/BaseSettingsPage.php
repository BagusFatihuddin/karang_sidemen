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
use Illuminate\Validation\ValidationException;

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
        $uploadField = $this->uploadFieldName($settingKey);
        $uploadState = $data[$uploadField] ?? $this->data[$uploadField] ?? null;

        $uploadedUrl = $this->uploadSettingsImageFromState($uploadState, $uploadField, $folder);

        if ($uploadedUrl) {
            $data[$settingKey] = $uploadedUrl;
        }
    }

    protected function uploadSettingsImageFromState(
        mixed $uploadState,
        string $uploadField,
        string $folder = 'website-public-media'
    ): ?string {
        if (! $uploadState) {
            return null;
        }

        try {
            $uploadedFile = $this->resolveUploadedFile($uploadState);

            if (! $uploadedFile) {
                $this->failSettingsImageUpload(
                    $uploadField,
                    'Upload gambar gagal. File tidak bisa diproses, silakan pilih ulang gambar.'
                );
            }

            $this->validateSettingsImageUpload($uploadedFile, $uploadField);

            $result = app(CloudinaryService::class)->upload($uploadedFile, $folder);

            if (blank($result['url'] ?? null)) {
                $this->failSettingsImageUpload(
                    $uploadField,
                    'Upload gambar gagal. URL gambar tidak diterima dari server upload.'
                );
            }

            return $result['url'];
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::warning('Settings media upload failed', [
                'upload_field' => $uploadField,
                'message' => $e->getMessage(),
            ]);

            $this->failSettingsImageUpload(
                $uploadField,
                'Upload gambar gagal. ' . $e->getMessage()
            );
        }
    }

    private function validateSettingsImageUpload(UploadedFile $uploadedFile, string $uploadField): void
    {
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];
        $maxKilobytes = 2048;

        if (str_contains($uploadField, 'brand_logo')) {
            $allowedMimeTypes[] = 'image/svg+xml';
        }

        $mimeType = strtolower((string) $uploadedFile->getMimeType());

        if (! in_array($mimeType, $allowedMimeTypes, true)) {
            $message = str_contains($uploadField, 'brand_logo')
                ? 'Tipe file tidak sesuai. Gunakan gambar JPG, PNG, WEBP, atau SVG.'
                : 'Tipe file tidak sesuai. Gunakan gambar JPG, PNG, atau WEBP.';

            $this->failSettingsImageUpload($uploadField, $message);
        }

        if ($uploadedFile->getSize() > ($maxKilobytes * 1024)) {
            $this->failSettingsImageUpload(
                $uploadField,
                'Ukuran gambar terlalu besar. Maksimal ' . (int) ($maxKilobytes / 1024) . ' MB.'
            );
        }
    }

    private function failSettingsImageUpload(string $uploadField, string $message): never
    {
        Notification::make()
            ->title('Upload gambar gagal')
            ->body($message)
            ->danger()
            ->send();

        throw ValidationException::withMessages([
            'data.' . $uploadField => $message,
        ]);
    }

    private function resolveUploadedFile(mixed $uploadState): ?UploadedFile
    {
        if (is_array($uploadState)) {
            $uploadState = reset($uploadState);
        }

        if ($uploadState instanceof UploadedFile) {
            return $uploadState;
        }

        $path = $this->extractUploadPath($uploadState);

        if (! $path) {
            return null;
        }

        $fullPath = is_file($path) ? $path : Storage::disk('local')->path($path);

        if (! is_file($fullPath)) {
            return null;
        }

        return new UploadedFile(
            $fullPath,
            basename($fullPath),
            mime_content_type($fullPath) ?: 'image/jpeg',
            test: true
        );
    }

    private function extractUploadPath(mixed $uploadState): ?string
    {
        if (is_string($uploadState)) {
            return $uploadState;
        }

        if (is_object($uploadState)) {
            foreach (['getRealPath', 'getPathname'] as $method) {
                if (method_exists($uploadState, $method)) {
                    $path = $uploadState->{$method}();

                    return is_string($path) ? $path : null;
                }
            }
        }

        return null;
    }
}
