<x-filament::section>
    <x-slot name="heading">
        Aksi Cepat
    </x-slot>

    <x-slot name="description">
        Pintasan kerja harian untuk mengelola konten, pengunjung, dan operasional desa wisata.
    </x-slot>

    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($actions as $action)
            @php
                $tone = $action['tone'] ?? 'gray';
                $toneClasses = match ($tone) {
                    'success' => 'border-emerald-500/20 bg-emerald-50 text-emerald-950 dark:border-emerald-400/20 dark:bg-emerald-950/30 dark:text-emerald-50',
                    'warning' => 'border-amber-500/20 bg-amber-50 text-amber-950 dark:border-amber-400/20 dark:bg-amber-950/30 dark:text-amber-50',
                    'info' => 'border-sky-500/20 bg-sky-50 text-sky-950 dark:border-sky-400/20 dark:bg-sky-950/30 dark:text-sky-50',
                    default => 'border-gray-500/15 bg-gray-50 text-gray-950 dark:border-white/10 dark:bg-white/5 dark:text-white',
                };
            @endphp

            <a
                href="{{ $action['url'] }}"
                class="{{ $toneClasses }} group rounded-xl border p-4 transition hover:-translate-y-0.5 hover:shadow-lg"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold">
                            {{ $action['label'] }}
                        </p>
                        <p class="mt-1 text-xs opacity-70">
                            {{ $action['description'] }}
                        </p>
                    </div>

                    <span class="text-lg leading-none opacity-40 transition group-hover:translate-x-1 group-hover:opacity-80">
                        &rarr;
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</x-filament::section>
