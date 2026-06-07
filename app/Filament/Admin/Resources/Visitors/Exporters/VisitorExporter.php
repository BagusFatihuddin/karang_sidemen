<?php

namespace App\Filament\Admin\Resources\Visitors\Exporters;

use App\Models\Visitor;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class VisitorExporter extends Exporter
{
    protected static ?string $model = Visitor::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Nama'),

            ExportColumn::make('origin_category')
                ->label('Kategori Asal')
                ->formatStateUsing(
                    fn (?string $state): string => match ($state) {
                        'lombok_tengah' => 'Lombok Tengah',
                        'lombok_lainnya' => 'Lombok Lainnya',
                        'luar_lombok' => 'Luar Lombok',
                        'mancanegara' => 'Mancanegara',
                        default => $state ?? '-',
                    }
                ),

            ExportColumn::make('origin_city')
                ->label('Kota Asal'),

            ExportColumn::make('visit_type')
                ->label('Tipe Kunjungan')
                ->formatStateUsing(
                    fn (?string $state): string => match ($state) {
                        'sendiri' => 'Sendiri',
                        'pasangan' => 'Pasangan',
                        'keluarga' => 'Keluarga',
                        'rombongan' => 'Rombongan',
                        default => $state ?? '-',
                    }
                ),

            ExportColumn::make('group_size')
                ->label('Jumlah Grup'),

            ExportColumn::make('destination.name')
                ->label('Destinasi'),

            ExportColumn::make('recordedBy.name')
                ->label('Dicatat Oleh'),

            ExportColumn::make('visited_at')
                ->label('Tanggal Kunjungan'),
        ];
    }

    public static function getCompletedNotificationBody(
        Export $export
    ): string {
        $successfulRows =
            number_format($export->successful_rows);

        return "Export visitor selesai. {$successfulRows} data berhasil diexport.";
    }
}