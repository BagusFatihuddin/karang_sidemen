<x-filament-panels::page>
    <form wire:submit="verify">
        {{ $this->form }}

        <div class="mt-6" wire:visible="$this->booking !== null">
            <x-filament::button
                type="submit"
                size="lg"
                class="w-full sm:w-auto"
            >
                Tandai Sudah Datang
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
