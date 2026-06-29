<x-filament-panels::page>
    <form wire:submit="applyFilters">
        {{ $this->form }}

        <div class="mt-6 flex gap-3">
            <x-filament::button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="applyFilters"
            >
                Terapkan Filter
            </x-filament::button>

            <x-filament::button
                type="button"
                wire:click="exportToExcel"
                wire:loading.attr="disabled"
                wire:target="applyFilters"
                outlined
            >
                Export Excel
            </x-filament::button>

            <x-filament::button
                type="button"
                wire:click="exportToPdf"
                wire:loading.attr="disabled"
                wire:target="applyFilters"
                outlined
            >
                Export PDF
            </x-filament::button>

            <div wire:loading wire:target="applyFilters" class="text-sm text-gray-500">
                Memproses laporan...
            </div>
        </div>
    </form>

    <div class="mt-8 space-y-8">
        <section>
            <h2 class="mb-3 text-lg font-semibold text-gray-900">
                Kunjungan Harian
            </h2>

            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-700">Tanggal</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Destinasi</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Pengunjung</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Pendapatan</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($dailyVisits as $visit)
                            <tr>
                                <td class="px-4 py-3 text-gray-900">{{ $visit['date'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $visit['destination'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $visit['visitor_count'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $visit['revenue'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $visit['expense'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-gray-600">
                                    Tidak ada data kunjungan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2 class="mb-3 text-lg font-semibold text-gray-900">
                Summary Destinasi
            </h2>

            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-700">Destinasi</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Total Pengunjung</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Pendapatan</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($destinationSummary as $summary)
                            <tr>
                                <td class="px-4 py-3 text-gray-900">{{ $summary['destination'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $summary['total_visitors'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $summary['revenue'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $summary['expense'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-gray-600">
                                    Tidak ada summary destinasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2 class="mb-3 text-lg font-semibold text-gray-900">
                Asal Wisatawan
            </h2>

            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-700">Kategori</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Jumlah</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($originBreakdown as $origin)
                            <tr>
                                <td class="px-4 py-3 text-gray-900">{{ $origin['label'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $origin['count'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $origin['percentage'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-gray-600">
                                    Tidak ada data asal wisatawan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2 class="mb-3 text-lg font-semibold text-gray-900">
                Referral Source
            </h2>

            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-700">Sumber</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Jumlah</th>
                            <th class="px-4 py-3 font-medium text-gray-700">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($referralBreakdown as $referral)
                            <tr>
                                <td class="px-4 py-3 text-gray-900">{{ $referral['label'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $referral['count'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $referral['percentage'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-gray-600">
                                    Tidak ada data referral.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-filament-panels::page>
