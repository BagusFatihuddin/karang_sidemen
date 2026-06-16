<x-filament-widgets::widget>
    <div
        class="relative overflow-hidden rounded-[28px] border border-white/8 bg-gradient-to-br from-[#103c2d] via-[#174836] to-[#0f2d22] p-8 shadow-2xl"
    >
        <div
            class="absolute right-0 top-0 h-48 w-48 translate-x-16 -translate-y-16 rounded-full bg-white/5 blur-3xl"
        ></div>

        <div class="relative z-10 flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <div
                    class="mb-5 inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1.5 text-xs font-medium text-emerald-300"
                >
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Sistem aktif
                </div>

                <p class="text-sm text-primary-100/80">
                    {{ $greeting }},
                </p>

                <h2 class="mt-2 text-4xl font-bold tracking-tight text-white">
                    {{ $name }} 👋
                </h2>

                <p class="mt-4 text-sm leading-7 text-primary-100/75">
                    Ringkasan operasional wisata hari ini.
                    Kelola destinasi, booking, pengunjung,
                    dan laporan dari satu dashboard.
                </p>

                <div class="mt-5 flex items-center gap-2 text-sm text-primary-100/60">
                    <span>📅</span>
                    <span>{{ $date }}</span>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-4">
                <div
                    class="rounded-3xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur-xl"
                >
                    <p class="text-[11px] font-medium uppercase tracking-[0.16em] text-primary-200/70">
                        Role
                    </p>

                    <p class="mt-1 text-lg font-semibold text-white">
                        {{ $role }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>