<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($this->cards() as $card)
            <a
                href="{{ $card['url'] }}"
                class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-500 hover:shadow-md dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="mb-4 flex items-center justify-between">
                    <span class="rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-primary-700 dark:bg-primary-500/10 dark:text-primary-300">
                        {{ $card['tone'] }}
                    </span>
                    <span class="text-lg text-gray-300 transition group-hover:translate-x-1 group-hover:text-primary-500">
                        &rarr;
                    </span>
                </div>

                <h2 class="text-base font-semibold text-gray-950 dark:text-white">
                    {{ $card['label'] }}
                </h2>

                <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                    {{ $card['description'] }}
                </p>
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
