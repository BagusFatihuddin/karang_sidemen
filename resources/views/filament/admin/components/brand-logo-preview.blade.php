@php
    use App\Support\AppSettings;
    
    // Get logo URL from form state or database
    $currentLogoUrl = $logoUrl ?? AppSettings::get('brand_logo_url');
    $currentLogoAlt = $logoAlt ?? AppSettings::get('brand_logo_alt');
@endphp

<div class="fi-component">
    @if($currentLogoUrl)
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-900/30">
            <div class="flex flex-col gap-3">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    ✨ Logo Saat Ini
                </p>
                <div class="flex items-center justify-center rounded-md bg-white p-8 dark:bg-gray-800">
                    <img 
                        src="{{ $currentLogoUrl }}" 
                        alt="{{ $currentLogoAlt ?? 'Logo Situs' }}"
                        class="h-auto max-h-48 max-w-xs object-contain"
                        loading="lazy"
                    />
                </div>
                @if($currentLogoAlt)
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <span class="font-medium">Deskripsi:</span> {{ $currentLogoAlt }}
                    </p>
                @endif
            </div>
        </div>
    @else
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-6 dark:border-amber-900 dark:bg-amber-950/20">
            <p class="flex items-center gap-2 text-sm text-amber-800 dark:text-amber-200">
                <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>Belum ada logo yang diupload. Gunakan form di bawah untuk mengunggah logo baru.</span>
            </p>
        </div>
    @endif
</div>

