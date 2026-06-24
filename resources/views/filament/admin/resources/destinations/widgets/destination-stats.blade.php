<div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-6">
    <!-- Total Destinations -->
    <div class="fi-overview-stat-card group relative rounded-2xl border p-6 transition duration-300 ease-out hover:-translate-y-1"
         style="
             border-color: rgba(59, 130, 246, 0.3);
             background: linear-gradient(to bottom right, rgba(59, 130, 246, 0.08), rgba(59, 130, 246, 0.02));
         ">
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-2 flex-1">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Destinasi</span>
                <span class="text-3xl font-bold text-gray-950 dark:text-white">{{ $totalDestinations }}</span>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-xl" style="background-color: rgba(59, 130, 246, 0.25);">
                📍
            </div>
        </div>
    </div>

    <!-- Active Destinations -->
    <div class="fi-overview-stat-card group relative rounded-2xl border p-6 transition duration-300 ease-out hover:-translate-y-1"
         style="
             border-color: rgba(16, 185, 129, 0.3);
             background: linear-gradient(to bottom right, rgba(16, 185, 129, 0.08), rgba(16, 185, 129, 0.02));
         ">
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-2 flex-1">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Destinasi Aktif</span>
                <span class="text-3xl font-bold text-gray-950 dark:text-white">{{ $activeDestinations }}</span>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-xl" style="background-color: rgba(16, 185, 129, 0.25);">
                ✅
            </div>
        </div>
    </div>

    <!-- Featured Destinations -->
    <div class="fi-overview-stat-card group relative rounded-2xl border p-6 transition duration-300 ease-out hover:-translate-y-1"
         style="
             border-color: rgba(251, 146, 60, 0.3);
             background: linear-gradient(to bottom right, rgba(251, 146, 60, 0.08), rgba(251, 146, 60, 0.02));
         ">
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-2 flex-1">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Fitur Homepage</span>
                <span class="text-3xl font-bold text-gray-950 dark:text-white">{{ $featuredDestinations }}</span>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-xl" style="background-color: rgba(251, 146, 60, 0.25);">
                ⭐
            </div>
        </div>
    </div>

    <!-- Types Distribution -->
    <div class="fi-overview-stat-card group relative rounded-2xl border p-6 transition duration-300 ease-out hover:-translate-y-1"
         style="
             border-color: rgba(168, 85, 247, 0.3);
             background: linear-gradient(to bottom right, rgba(168, 85, 247, 0.08), rgba(168, 85, 247, 0.02));
         ">
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-2 flex-1">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Jenis Destinasi</span>
                <span class="text-xl font-bold text-gray-950 dark:text-white">{{ count($destinationsByType) }} Tipe</span>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 space-y-1">
                    @foreach($destinationsByType as $type => $count)
                        <div>{{ ucfirst($type) }}: {{ $count }}</div>
                    @endforeach
                </div>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-xl" style="background-color: rgba(168, 85, 247, 0.25);">
                🗂️
            </div>
        </div>
    </div>
</div>
