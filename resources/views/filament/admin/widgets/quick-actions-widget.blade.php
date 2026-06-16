<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Aksi Cepat
        </x-slot>

        <x-slot name="description">
            Pintasan kerja harian untuk mengelola konten, pengunjung, dan operasional desa wisata.
        </x-slot>

        <div class="fi-not-prose">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3 text-sm">
                @foreach ($actions as $action)
                    @php
                        $icons = [
                            'Registrasi Wisatawan' => '👥',
                            'Verifikasi Booking' => '📋',
                            'Tambah Destinasi' => '📍',
                            'Tambah Paket' => '🗺️',
                            'Tambah Event' => '📅',
                            'Tambah Guide' => '🧑‍💼',
                            'Moderasi Review' => '⭐',
                            'Website Media' => '🖼️',
                            'Laporan' => '📊',
                            'Lihat Laporan' => '📊',
                        ];

                        $emoji = $icons[$action['label']] ?? '→';
                    @endphp

                    <a
                        href="{{ $action['url'] }}"
                        class="group flex items-start gap-3 rounded-2xl border border-gray-800 bg-white/[0.03] p-4 transition hover:border-primary-500/30 hover:bg-white/[0.05]"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-500/10 text-base">
                            {{ $emoji }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="truncate text-sm font-semibold text-white">
                                    {{ $action['label'] }}
                                </h3>

                                <span class="text-gray-500 transition group-hover:translate-x-1">
                                    →
                                </span>
                            </div>

                            <p class="mt-1 text-xs leading-relaxed text-gray-400">
                                {{ $action['description'] }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>