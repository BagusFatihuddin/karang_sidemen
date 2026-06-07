<x-filament-panels::page>
    <form wire:submit="register">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button
                type="submit"
                size="lg"
                class="w-full sm:w-auto"
            >
                Simpan Data Wisatawan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
