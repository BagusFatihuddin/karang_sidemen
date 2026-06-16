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
                            class="group relative overflow-hidden rounded-2xl border transition duration-300 p-5 hover:-translate-y-1"
                            style="
                                border-color: rgba(255, 255, 255, 0.1);
                                background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02));
                            "
                        >
                            <!-- Background gradient on hover -->
                            <div class="absolute inset-0 -z-10 opacity-0 transition duration-300 group-hover:opacity-100"></div>

                            <!-- Icon & Arrow -->
                            <div class="mb-4 flex items-start justify-between">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg text-lg" style="background-color: rgba(255, 255, 255, 0.08)">
                                    <x-dynamic-component component="heroicon-m-{{ str_replace('heroicon-m-', '', $card['icon']) }}" class="h-5 w-5 text-primary-500" />
                                </div>
                                <span class="text-lg text-gray-400 transition group-hover:translate-x-1 group-hover:text-primary-500">
                                    →
                                </span>
                            </div>

                            <!-- Content -->
                            <div>
                                <h3 class="font-semibold text-white">
                                    {{ $card['label'] }}
                                </h3>
                                <p class="mt-2 text-sm leading-relaxed text-gray-400 group-hover:text-gray-300">
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
