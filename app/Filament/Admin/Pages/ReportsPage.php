<?php

namespace App\Filament\Admin\Pages;

use App\Exports\VisitReportExport;
use App\Exports\VisitReportPdf;
use App\Models\DailyVisit;
use App\Models\Destination;
use App\Models\Visitor;
use App\Support\UserRole;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view =
        'filament.admin.pages.reports-page';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedDocumentChartBar;

    protected static ?string $navigationLabel = 'Laporan';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $title = 'Laporan';

    protected static ?string $slug = 'reports';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public array $dailyVisits = [];

    public array $destinationSummary = [];

    public array $originBreakdown = [];

    public array $referralBreakdown = [];

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
                UserRole::PIMPINAN,
                UserRole::ANGGOTA_POKDARWIS,
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
                Section::make('Filter Laporan')
                    ->schema([
                        Select::make('destination_ids')
                            ->label('Destinasi')
                            ->multiple()
                            ->options(
                                fn (): array => Destination::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all()
                            )
                            ->searchable()
                            ->native(false),

                        DatePicker::make('date_from')
                            ->label('Dari Tanggal')
                            ->required(),

                        DatePicker::make('date_until')
                            ->label('Sampai Tanggal')
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }

    public function applyFilters(): void
    {
        $data = $this->form->getState();
        $destinationIds = array_values(
            array_filter($data['destination_ids'] ?? [])
        );

        $this->dailyVisits = $this->buildDailyVisits(
            $data,
            $destinationIds
        );

        $this->destinationSummary = $this->buildDestinationSummary(
            $data,
            $destinationIds
        );

        $this->originBreakdown = $this->buildVisitorBreakdown(
            'origin_category',
            $data,
            $destinationIds
        );

        $this->referralBreakdown = $this->buildVisitorBreakdown(
            'referral_source',
            $data,
            $destinationIds
        );
    }

    protected function buildDailyVisits(
        array $data,
        array $destinationIds
    ): array {
        return DailyVisit::query()
            ->with('destination')
            ->when(
                filled($destinationIds),
                fn (Builder $query) => $query->whereIn(
                    'destination_id',
                    $destinationIds
                )
            )
            ->whereDate('date', '>=', $data['date_from'])
            ->whereDate('date', '<=', $data['date_until'])
            ->orderByDesc('date')
            ->get()
            ->map(
                fn (DailyVisit $visit): array => [
                    'date' => $visit->date?->format('d-m-Y'),
                    'destination' => $visit->destination?->name ?? '-',
                    'visitor_count' => $visit->visitor_count,
                    'revenue' => $this->formatCurrency($visit->revenue),
                    'expense' => $this->formatCurrency($visit->expense),
                ]
            )
            ->all();
    }

    protected function buildDestinationSummary(
        array $data,
        array $destinationIds
    ): array {
        return DailyVisit::query()
            ->with('destination')
            ->select('destination_id')
            ->selectRaw('SUM(visitor_count) as total_visitors')
            ->selectRaw('SUM(revenue) as total_revenue')
            ->selectRaw('SUM(expense) as total_expense')
            ->when(
                filled($destinationIds),
                fn (Builder $query) => $query->whereIn(
                    'destination_id',
                    $destinationIds
                )
            )
            ->whereDate('date', '>=', $data['date_from'])
            ->whereDate('date', '<=', $data['date_until'])
            ->groupBy('destination_id')
            ->orderBy('destination_id')
            ->get()
            ->map(
                fn (DailyVisit $summary): array => [
                    'destination' => $summary->destination?->name ?? '-',
                    'total_visitors' => (int) $summary->total_visitors,
                    'revenue' => $this->formatCurrency(
                        $summary->total_revenue
                    ),
                    'expense' => $this->formatCurrency(
                        $summary->total_expense
                    ),
                ]
            )
            ->all();
    }

    protected function buildVisitorBreakdown(
        string $column,
        array $data,
        array $destinationIds
    ): array {
        $baseQuery = Visitor::query()
            ->when(
                filled($destinationIds),
                fn (Builder $query) => $query->whereIn(
                    'destination_id',
                    $destinationIds
                )
            )
            ->whereDate('visited_at', '>=', $data['date_from'])
            ->whereDate('visited_at', '<=', $data['date_until']);

        $total = (clone $baseQuery)->count();

        return (clone $baseQuery)
            ->select($column)
            ->selectRaw('COUNT(*) as total')
            ->groupBy($column)
            ->orderBy($column)
            ->get()
            ->map(
                fn (Visitor $row): array => [
                    'label' => $row->{$column} ?? '-',
                    'count' => (int) $row->total,
                    'percentage' => $total > 0
                        ? number_format(
                            ((int) $row->total / $total) * 100,
                            1
                        ) . '%'
                        : '0.0%',
                ]
            )
            ->all();
    }

    protected function formatCurrency(mixed $value): string
    {
        return 'Rp ' . number_format(
            (float) $value,
            0,
            ',',
            '.'
        );
    }

    protected function defaultFormData(): array
    {
        return [
            'destination_ids' => [],
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_until' => now()->endOfMonth()->toDateString(),
        ];
    }

    public function exportToExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = $this->generateFilename('xlsx');

        return Excel::download(
            new VisitReportExport(
                $this->dailyVisits,
                $this->destinationSummary,
                $this->originBreakdown,
                $this->referralBreakdown
            ),
            $filename
        );
    }

    public function exportToPdf()
    {
        $data = $this->form->getState();
        $filename = $this->generateFilename('pdf');

        $pdf = new VisitReportPdf(
            $this->dailyVisits,
            $this->destinationSummary,
            $this->originBreakdown,
            $this->referralBreakdown,
            $data['date_from'],
            $data['date_until']
        );

        return $pdf->download($filename);
    }

    protected function generateFilename(string $format): string
    {
        $data = $this->form->getState();
        $dateFrom = \Carbon\Carbon::parse($data['date_from']);
        $month = $dateFrom->format('m');
        $year = $dateFrom->format('Y');

        return "laporan-kunjungan-{$month}-{$year}.{$format}";
    }
}
