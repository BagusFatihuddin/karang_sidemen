<x-filament-panels::page>
    <!-- Breadcrumb & Description -->
    <div class="mb-8 rounded-xl border border-white/10 bg-gradient-to-br from-white/5 to-white/0 p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-400">⚙️ Pengaturan Website</p>
                <h1 class="mt-2 text-2xl font-bold text-white">
                    {{ $this->getTitle() }}
                </h1>
                @if ($subtitle = $this->getSubheading())
                    <p class="mt-2 text-sm leading-relaxed text-gray-300">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Form -->
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <!-- Submit Section -->
        <div class="flex items-center justify-end gap-3 border-t border-white/10 pt-6">
            <p class="text-xs text-gray-500">💾 Semua perubahan akan disimpan otomatis</p>
            <x-filament::button type="submit" class="bg-primary-600 hover:bg-primary-700">
                ✓ Simpan Pengaturan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
