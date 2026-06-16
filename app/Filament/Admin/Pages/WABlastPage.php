<?php

namespace App\Filament\Admin\Pages;

use App\Models\Destination;
use App\Models\Visitor;
use App\Support\UserRole;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class WABlastPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view =
        'filament.admin.pages.wa-blast-page';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'WA Blast';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengunjung';

    protected static ?string $title = 'WA Blast';

    protected static ?string $slug = 'wa-blast';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public bool $hasSearched = false;

    public array $results = [];

    public array $bulkLinks = [];

    public int $currentPage = 1;

    public int $perPage = 15;

    public int $totalResults = 0;

    public int $totalPages = 0;

    public function mount(): void
    {
        abort_unless(
            static::allowed(),
            403
        );

        $this->form->fill(
            $this->defaultFormData()
        );
    }

    protected static function allowed(): bool
    {
        return in_array(
            Auth::user()?->role,
            [
                UserRole::SUPER_ADMIN,
                UserRole::ADMIN_KONTEN,
            ],
            true
        );
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::allowed();
    }

    public static function canAccess(): bool
    {
        return static::allowed();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Filter Wisatawan')
                    ->schema([
                        Select::make('origin_category')
                            ->label('🏘️ Kategori Asal')
                            ->options([
                                'lombok_tengah' => 'Lombok Tengah',
                                'lombok_lainnya' => 'Lombok Lainnya',
                                'luar_lombok' => 'Luar Lombok',
                                'mancanegara' => 'Mancanegara',
                            ])
                            ->native(false)
                            ->placeholder('Pilih kategori asal...'),

                        Select::make('visit_type')
                            ->label('👥 Tipe Kunjungan')
                            ->options([
                                'sendiri' => 'Sendiri',
                                'pasangan' => 'Pasangan',
                                'keluarga' => 'Keluarga',
                                'rombongan' => 'Rombongan',
                            ])
                            ->native(false)
                            ->placeholder('Pilih tipe kunjungan...'),

                        Select::make('destination_id')
                            ->label('📍 Destinasi')
                            ->multiple()
                            ->options(
                                fn (): array => Destination::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all()
                            )
                            ->searchable()
                            ->native(false)
                            ->placeholder('Pilih satu atau lebih destinasi...'),

                        Textarea::make('message')
                            ->label('📨 Pesan')
                            ->required()
                            ->rows(4)
                            ->placeholder('Contoh: Halo [nama], terima kasih telah berkunjung ke desa wisata kami. Kami senang melihat Anda! Jika ada pertanyaan, silakan hubungi kami.')
                            ->helperText('Gunakan [nama] untuk mengganti dengan nama wisatawan. Pesan akan disesuaikan untuk setiap penerima.'),
                    ])
                    ->columns(1),
            ]);
    }

    public function search(): void
    {
        // Reset pagination when searching
        $this->currentPage = 1;
        $this->loadResults();
    }

    public function goToPage(int $page): void
    {
        $this->currentPage = max(1, min($page, $this->totalPages));
        $this->loadResults();
    }

    private function loadResults(): void
    {
        $data = $this->form->getState();

        $query = Visitor::query()
            ->with('destination')
            ->when(
                filled($data['origin_category'] ?? null),
                fn ($query) => $query->where(
                    'origin_category',
                    $data['origin_category']
                )
            )
            ->when(
                filled($data['visit_type'] ?? null),
                fn ($query) => $query->where(
                    'visit_type',
                    $data['visit_type']
                )
            )
            ->when(
                filled($data['destination_id'] ?? []),
                fn ($query) => $query->whereIn(
                    'destination_id',
                    $data['destination_id']
                )
            )
            ->orderByDesc('visited_at')
            ->orderByDesc('id'); // Secondary sort untuk consistency

        // Get total count
        $this->totalResults = $query->count();
        $this->totalPages = (int) ceil($this->totalResults / $this->perPage);

        // Ensure currentPage is valid
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        }

        // Get paginated results
        $visitors = $query
            ->skip(($this->currentPage - 1) * $this->perPage)
            ->take($this->perPage)
            ->get();

        $message = (string) ($data['message'] ?? '');

        $this->results = $visitors
            ->map(function (Visitor $visitor) use ($message): array {
                return [
                    'name' => $visitor->name,
                    'whatsapp_number' => $visitor->whatsapp_number,
                    'destination' => $visitor->destination?->name ?? '-',
                    'url' => $this->makeWaUrl(
                        $visitor->whatsapp_number,
                        $message,
                        $visitor->name
                    ),
                ];
            })
            ->all();

        // Bulk links hanya untuk page saat ini
        $this->bulkLinks = collect($this->results)
            ->pluck('url')
            ->filter()
            ->values()
            ->all();

        $this->hasSearched = true;
    }

    public function makeWaUrl(
        string $phone,
        string $message,
        string $name
    ): ?string {
        $normalizedPhone = $this->normalizePhone($phone);

        if ($normalizedPhone === null) {
            return null;
        }

        $message = str_replace(
            '[nama]',
            $name,
            $message
        );

        return 'https://wa.me/' . $normalizedPhone .
            '?text=' . urlencode($message);
    }

    public function normalizePhone(string $phone): ?string
    {
        $phone = trim($phone);
        $phone = str_replace([' ', '-'], '', $phone);

        if (str_starts_with($phone, '+628')) {
            return substr($phone, 1);
        }

        if (str_starts_with($phone, '628')) {
            return $phone;
        }

        if (str_starts_with($phone, '08')) {
            return '62' . substr($phone, 1);
        }

        return null;
    }

    protected function defaultFormData(): array
    {
        return [
            'destination_id' => [],
            'message' => 'Halo [nama], terima kasih telah berkunjung ke desa wisata kami.',
        ];
    }
}
