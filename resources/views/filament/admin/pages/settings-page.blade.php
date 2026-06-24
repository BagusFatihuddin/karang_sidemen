<x-filament-panels::page>
    <div class="space-y-8">
        @foreach ($this->cards() as $category => $cards)
            <div>
                <!-- Category Header -->
                <div class="mb-4 flex items-center gap-2">
                    <div class="h-1 w-12 rounded-full" style="background-color: hsl(var(--primary-500))"></div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $category }}
                    </h2>
                </div>

                <!-- Cards Grid -->
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($cards as $card)
                        <a
                            href="{{ $card['url'] }}"
                            class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-primary-300 hover:shadow-md dark:border-white/10 dark:bg-white/5 dark:shadow-none"
                        >
                            <!-- Background gradient on hover -->
                            <div class="absolute inset-0 -z-10 opacity-0 transition duration-300 group-hover:opacity-100"></div>

                            <!-- Icon & Arrow -->
                            <div class="mb-4 flex items-start justify-between">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 text-lg dark:bg-white/10">
                                    <x-dynamic-component component="heroicon-m-{{ str_replace('heroicon-m-', '', $card['icon']) }}" class="h-5 w-5 text-primary-500" />
                                </div>
                                <span class="text-lg text-gray-400 transition group-hover:translate-x-1 group-hover:text-primary-500">
                                    →
                                </span>
                            </div>

                            <!-- Content -->
                            <div>
                                <h3 class="font-semibold text-gray-950 dark:text-white">
                                    {{ $card['label'] }}
                                </h3>
                                <p class="mt-2 text-sm leading-relaxed text-gray-600 group-hover:text-gray-800 dark:text-gray-400 dark:group-hover:text-gray-300">
                                    {{ $card['description'] }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
