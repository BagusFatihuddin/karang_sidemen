<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class VisitReportDailySheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(
        private array $data,
        private string $title
    ) {}

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return match ($this->title) {
            'Kunjungan Harian' => ['Tanggal', 'Destinasi', 'Pengunjung', 'Pendapatan', 'Pengeluaran'],
            'Summary Destinasi' => ['Destinasi', 'Total Pengunjung', 'Pendapatan', 'Pengeluaran'],
            'Asal Wisatawan' => ['Kategori', 'Jumlah', 'Persentase'],
            'Referral Source' => ['Sumber', 'Jumlah', 'Persentase'],
            default => [],
        };
    }

    public function title(): string
    {
        return $this->title;
    }
}

class VisitReportExport implements WithMultipleSheets
{
    public function __construct(
        private array $dailyVisits,
        private array $destinationSummary,
        private array $originBreakdown,
        private array $referralBreakdown
    ) {}

    public function sheets(): array
    {
        return [
            new VisitReportDailySheet(
                $this->transformDailyVisits(),
                'Kunjungan Harian'
            ),
            new VisitReportDailySheet(
                $this->transformDestinationSummary(),
                'Summary Destinasi'
            ),
            new VisitReportDailySheet(
                $this->transformOriginBreakdown(),
                'Asal Wisatawan'
            ),
            new VisitReportDailySheet(
                $this->transformReferralBreakdown(),
                'Referral Source'
            ),
        ];
    }

    private function transformDailyVisits(): array
    {
        return array_map(
            fn (array $visit): array => [
                $visit['date'],
                $visit['destination'],
                $visit['visitor_count'],
                $visit['revenue'],
                $visit['expense'],
            ],
            $this->dailyVisits
        );
    }

    private function transformDestinationSummary(): array
    {
        return array_map(
            fn (array $summary): array => [
                $summary['destination'],
                $summary['total_visitors'],
                $summary['revenue'],
                $summary['expense'],
            ],
            $this->destinationSummary
        );
    }

    private function transformOriginBreakdown(): array
    {
        return array_map(
            fn (array $origin): array => [
                $origin['label'],
                $origin['count'],
                $origin['percentage'],
            ],
            $this->originBreakdown
        );
    }

    private function transformReferralBreakdown(): array
    {
        return array_map(
            fn (array $referral): array => [
                $referral['label'],
                $referral['count'],
                $referral['percentage'],
            ],
            $this->referralBreakdown
        );
    }
}
