<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Aksi Cepat
        </x-slot>

        <x-slot name="description">
            Pintasan kerja harian untuk mengelola konten, pengunjung, dan operasional desa wisata.
        </x-slot>

        <div class="fi-not-prose">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
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

                        $colorMap = [
                            'Registrasi Wisatawan' => '#10b981',
                            'Verifikasi Booking' => '#f59e0b',
                            'Tambah Destinasi' => '#0ea5e9',
                            'Tambah Paket' => '#06b6d4',
                            'Tambah Event' => '#a855f7',
                            'Tambah Guide' => '#22c55e',
                            'Moderasi Review' => '#f43f5e',
                            'Website Media' => '#6366f1',
                            'Laporan' => '#3b82f6',
                            'Lihat Laporan' => '#3b82f6',
                        ];

                        $emoji = $icons[$action['label']] ?? '→';
                        $color = $colorMap[$action['label']] ?? '#6b7280';
                    @endphp

                    <a
                        href="{{ $action['url'] }}"
                        class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition duration-300 ease-out hover:-translate-y-1.5 hover:shadow-md dark:border-white/10 dark:bg-white/5 dark:shadow-none"
                    >
                        <div
                            class="absolute inset-0 -z-10 opacity-0 transition duration-300 group-hover:opacity-100"
                            style="background: linear-gradient(to bottom right, {{ $color }}15, transparent);"
                        ></div>

                        <div class="flex items-start justify-between gap-4">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-xl ring-1 transition duration-300 group-hover:scale-110"
                                style="
                                    background-color: {{ $color }}25;
                                    color: {{ $color }};
                                    ring-color: {{ $color }}50;
                                "
                            >
                                {{ $emoji }}
                            </div>

                            <span class="mt-1 text-lg leading-none text-gray-600 opacity-0 transition duration-300 group-hover:translate-x-1 group-hover:opacity-100">
                                →
                            </span>
                        </div>

                        <div class="mt-4">
                            <h3 class="font-semibold text-gray-950 leading-snug dark:text-white">
                                {{ $action['label'] }}
                            </h3>

                            <p class="mt-2 text-xs text-gray-600 leading-relaxed dark:text-gray-400">
                                {{ $action['description'] }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>