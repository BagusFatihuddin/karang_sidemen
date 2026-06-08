<?php

namespace App\Exports;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class VisitReportPdf
{
    public function __construct(
        private array $dailyVisits,
        private array $destinationSummary,
        private array $originBreakdown,
        private array $referralBreakdown,
        private string $dateFrom,
        private string $dateUntil
    ) {}

    public function download(string $filename): Response
    {
        $pdf = Pdf::loadView('exports.report-pdf', [
            'dailyVisits' => $this->dailyVisits,
            'destinationSummary' => $this->destinationSummary,
            'originBreakdown' => $this->originBreakdown,
            'referralBreakdown' => $this->referralBreakdown,
            'dateFrom' => $this->dateFrom,
            'dateUntil' => $this->dateUntil,
        ])->setOption('encoding', 'UTF-8');

        $content = $pdf->output();

        return response()->streamDownload(
            function() use ($content) {
                echo $content;
            },
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }
}
