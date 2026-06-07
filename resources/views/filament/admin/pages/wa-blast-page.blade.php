<x-filament-panels::page>
    <form wire:submit="search">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                Cari Wisatawan
            </x-filament::button>
        </div>
    </form>

    <div class="mt-8">
        @if (! $hasSearched)
            <div class="rounded-lg border border-gray-200 p-6 text-sm text-gray-600">
                Gunakan filter lalu klik Cari Wisatawan.
            </div>
        @elseif (count($results) === 0)
            <div class="rounded-lg border border-gray-200 p-6 text-sm text-gray-600">
                Tidak ada wisatawan yang sesuai filter.
            </div>
        @else
            <div class="mb-4">
                <x-filament::button
                    type="button"
                    color="gray"
                    x-data="{ links: @js($bulkLinks) }"
                    x-on:click="
                        if (links.length > 5 && ! confirm('Buka ' + links.length + ' nomor WhatsApp?')) {
                            return;
                        }

                        links.forEach((link) => window.open(link, '_blank'));
                    "
                >
                    Buka Semua
                </x-filament::button>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-700">Nama</th>
                            <th class="px-4 py-3 font-medium text-gray-700">WhatsApp</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Destinasi</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($results as $result)
                            <tr>
                                <td class="px-4 py-3 text-gray-900">
                                    {{ $result['name'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $result['whatsapp_number'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $result['destination'] }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($result['url'])
                                        <a
                                            href="{{ $result['url'] }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-primary-600 hover:text-primary-500 font-medium"
                                        >
                                            Buka WhatsApp
                                        </a>
                                    @else
                                        <span class="text-gray-500">
                                            Nomor tidak valid
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-filament-panels::page>
