<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #34495e;
        }
        .period {
            text-align: center;
            font-size: 11px;
            color: #555;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.report-table {
            page-break-inside: avoid;
        }
        thead {
            background-color: #34495e;
            color: white;
        }
        th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #bdc3c7;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #ecf0f1;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tbody tr:hover {
            background-color: #ecf0f1;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            color: #2c3e50;
            padding: 5px;
            background-color: #ecf0f1;
        }
        .empty-message {
            text-align: center;
            color: #7f8c8d;
            padding: 15px;
            font-style: italic;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #bdc3c7;
            text-align: center;
            font-size: 9px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <h1>POKDARWIS</h1>
        <h2 style="font-size: 13px; margin: 5px 0;">Laporan Kunjungan & Pendapatan</h2>
    </div>

    <div class="period">
        Periode: {{ $dateFrom }} s.d. {{ $dateUntil }}
    </div>

    @if (!empty($dailyVisits))
        <div class="section-title">Kunjungan Harian</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Destinasi</th>
                    <th class="text-right">Pengunjung</th>
                    <th class="text-right">Pendapatan</th>
                    <th class="text-right">Pengeluaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dailyVisits as $visit)
                    <tr>
                        <td>{{ $visit['date'] }}</td>
                        <td>{{ $visit['destination'] }}</td>
                        <td class="text-right">{{ $visit['visitor_count'] }}</td>
                        <td class="text-right">{{ $visit['revenue'] }}</td>
                        <td class="text-right">{{ $visit['expense'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-message">Tidak ada data kunjungan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if (!empty($destinationSummary))
        <div class="section-title">Summary Destinasi</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Destinasi</th>
                    <th class="text-right">Total Pengunjung</th>
                    <th class="text-right">Pendapatan</th>
                    <th class="text-right">Pengeluaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($destinationSummary as $summary)
                    <tr>
                        <td>{{ $summary['destination'] }}</td>
                        <td class="text-right">{{ $summary['total_visitors'] }}</td>
                        <td class="text-right">{{ $summary['revenue'] }}</td>
                        <td class="text-right">{{ $summary['expense'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-message">Tidak ada summary destinasi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if (!empty($originBreakdown))
        <div class="section-title">Asal Wisatawan</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-right">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($originBreakdown as $origin)
                    <tr>
                        <td>{{ $origin['label'] }}</td>
                        <td class="text-right">{{ $origin['count'] }}</td>
                        <td class="text-right">{{ $origin['percentage'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-message">Tidak ada data asal wisatawan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if (!empty($referralBreakdown))
        <div class="section-title">Referral Source</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Sumber</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-right">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($referralBreakdown as $referral)
                    <tr>
                        <td>{{ $referral['label'] }}</td>
                        <td class="text-right">{{ $referral['count'] }}</td>
                        <td class="text-right">{{ $referral['percentage'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-message">Tidak ada data referral</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}
    </div>
</body>
</html>
