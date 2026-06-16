<x-filament-panels::page>
    <form wire:submit="search">
        {{ $this->form }}

        <div class="mt-6 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-m-magnifying-glass">
                🔍 Cari Wisatawan
            </x-filament::button>
            <p class="mt-2 text-sm text-gray-600">Tekan Enter atau klik tombol untuk mencari</p>
        </div>
    </form>

    <div class="mt-8">
        @if (! $hasSearched)
            <div class="rounded-lg border-2 border-dashed border-blue-200 bg-blue-50 p-8 text-center dark:border-blue-900 dark:bg-blue-950/30">
                <div class="text-4xl mb-3">🔎</div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Mulai Cari Wisatawan</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                    Gunakan filter di atas untuk menemukan wisatawan berdasarkan kategori asal, tipe kunjungan, atau destinasi. Kemudian tulis pesan yang ingin Anda kirimkan.
                </p>
            </div>
        @elseif (count($results) === 0)
            <div class="rounded-lg border-2 border-dashed border-amber-200 bg-amber-50 p-8 text-center dark:border-amber-900 dark:bg-amber-950/30">
                <div class="text-4xl mb-3">😅</div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Tidak Ada Hasil</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                    Tidak ada wisatawan yang sesuai dengan filter yang Anda pilih. Coba ubah kriteria pencarian Anda.
                </p>
            </div>
        @else
            <div class="space-y-4">
                <!-- Results Summary -->
                <div class="rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 p-4 dark:from-green-950/30 dark:to-emerald-950/30 dark:border-green-900">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">✅</span>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Ditemukan {{ $totalResults }} Wisatawan</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Menampilkan {{ count($results) }} dari {{ $totalResults }} wisatawan di halaman ini</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $totalResults }}</div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Action Button -->
                <div class="flex gap-3">
                    <x-filament::button
                        type="button"
                        color="success"
                        size="lg"
                        icon="heroicon-m-chat-bubble-bottom-center-text"
                        x-data="{
                            links: {{ json_encode($bulkLinks) }},
                            count: {{ count($bulkLinks) }},
                            openAll() {
                                if (this.count > 5) {
                                    if (!confirm('Anda akan membuka ' + this.count + ' jendela WhatsApp di halaman ini. Lanjutkan?')) {
                                        return;
                                    }
                                }
                                let opened = 0;
                                this.links.forEach((link) => {
                                    setTimeout(() => {
                                        window.open(link, '_blank');
                                        opened++;
                                    }, opened * 200);
                                });
                            }
                        }"
                        @click="openAll()"
                    >
                        💬 Buka Semua di Halaman Ini ({{ count($bulkLinks) }})
                    </x-filament::button>
                    <span class="text-sm text-gray-600 dark:text-gray-400 mt-3">
                        Buka semua chat di halaman ini dengan delay untuk menghindari blok
                    </span>
                </div>

                <!-- Results Table -->
                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-800 text-left">
                                <tr>
                                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">👤 Nama</th>
                                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">📱 WhatsApp</th>
                                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">📍 Destinasi</th>
                                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white text-center">⚡ Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($results as $index => $result)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100 font-medium">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-xs font-semibold text-blue-700 dark:text-blue-300">
                                                    {{ ($currentPage - 1) * $perPage + $index + 1 }}
                                                </span>
                                                {{ $result['name'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                            <div class="flex items-center justify-between">
                                                <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                                                    {{ $result['whatsapp_number'] }}
                                                </code>
                                                <button 
                                                    type="button"
                                                    onclick="navigator.clipboard.writeText('{{ $result['whatsapp_number'] }}'); alert('Nomor disalin!')"
                                                    class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium ml-2"
                                                    title="Salin nomor"
                                                >
                                                    📋
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300">
                                                📍 {{ $result['destination'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($result['url'])
                                                <a
                                                    href="{{ $result['url'] }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-600 text-white text-xs font-semibold rounded-lg transition-colors"
                                                >
                                                    💬 Chat
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs font-semibold rounded-lg">
                                                    ❌ Nomor Invalid
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Results Footer with Pagination -->
                <div class="rounded-lg bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900 p-4 text-sm text-blue-800 dark:text-blue-300">
                    <strong>💡 Tip:</strong> Klik tombol "Buka Semua WhatsApp" untuk membuka semua chat di halaman ini, atau klik "Chat" di baris tertentu untuk membuka chat individual.
                </div>

                <!-- Pagination -->
                @if ($totalPages > 1)
                    <nav class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <strong>📄 Halaman {{ $currentPage }} dari {{ $totalPages }}</strong>
                                <br>
                                Menampilkan {{ ($currentPage - 1) * $perPage + 1 }}-{{ min($currentPage * $perPage, $totalResults) }} dari {{ $totalResults }} wisatawan
                            </div>

                            <div class="flex gap-2 flex-wrap justify-end">
                                @if ($currentPage > 1)
                                    <button 
                                        wire:click="goToPage(1)"
                                        class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium transition-colors"
                                        title="Halaman pertama"
                                    >
                                        ⬅️ Pertama
                                    </button>
                                    <button 
                                        wire:click="goToPage({{ $currentPage - 1 }})"
                                        class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium transition-colors"
                                        title="Halaman sebelumnya"
                                    >
                                        ◀ Sebelumnya
                                    </button>
                                @endif

                                <!-- Page Numbers -->
                                <div class="flex gap-1">
                                    @php
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($totalPages, $currentPage + 2);
                                    @endphp

                                    @if ($startPage > 1)
                                        <button 
                                            wire:click="goToPage(1)"
                                            class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                        >
                                            1
                                        </button>
                                        @if ($startPage > 2)
                                            <span class="text-gray-500 dark:text-gray-400 text-sm">...</span>
                                        @endif
                                    @endif

                                    @for ($page = $startPage; $page <= $endPage; $page++)
                                        @if ($page === $currentPage)
                                            <button 
                                                disabled
                                                class="w-10 h-10 rounded-lg bg-green-600 dark:bg-green-700 text-white text-sm font-bold cursor-default"
                                            >
                                                {{ $page }}
                                            </button>
                                        @else
                                            <button 
                                                wire:click="goToPage({{ $page }})"
                                                class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                            >
                                                {{ $page }}
                                            </button>
                                        @endif
                                    @endfor

                                    @if ($endPage < $totalPages)
                                        @if ($endPage < $totalPages - 1)
                                            <span class="text-gray-500 dark:text-gray-400 text-sm">...</span>
                                        @endif
                                        <button 
                                            wire:click="goToPage({{ $totalPages }})"
                                            class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                        >
                                            {{ $totalPages }}
                                        </button>
                                    @endif
                                </div>

                                @if ($currentPage < $totalPages)
                                    <button 
                                        wire:click="goToPage({{ $currentPage + 1 }})"
                                        class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium transition-colors"
                                        title="Halaman berikutnya"
                                    >
                                        Selanjutnya ▶
                                    </button>
                                    <button 
                                        wire:click="goToPage({{ $totalPages }})"
                                        class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium transition-colors"
                                        title="Halaman terakhir"
                                    >
                                        Terakhir ➡️
                                    </button>
                                @endif
                            </div>
                        </div>
                    </nav>
                @endif
            </div>
        @endif
    </div>
</x-filament-panels::page>
