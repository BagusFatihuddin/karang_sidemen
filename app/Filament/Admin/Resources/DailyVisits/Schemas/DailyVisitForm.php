<?php

namespace App\Filament\Admin\Resources\DailyVisits\Schemas;

use App\Models\Destination;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DailyVisitForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([
                Section::make('Data Kunjungan Harian')
                    ->schema([
                        Select::make('destination_id')
                            ->label('Destinasi')
                            ->options(
                                fn (): array => Destination::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all()
                            )
                            ->searchable()
                            ->required()
                            ->native(false),

                        DatePicker::make('date')
                            ->label('Tanggal')
                            ->default(today())
                            ->required(),

                        TextInput::make('visitor_count')
                            ->label('Jumlah Wisatawan')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        TextInput::make('revenue')
                            ->label('Pemasukan')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        TextInput::make('expense')
                            ->label('Pengeluaran')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(1),
            ]);
    }
}
