<?php

namespace App\Filament\Admin\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('visitor_id')
                    ->label('Wisatawan')
                    ->relationship(
                        name: 'visitor',
                        titleAttribute: 'name'
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->native(false),

                Select::make('destination_id')
                    ->label('Destinasi')
                    ->relationship(
                        name: 'destination',
                        titleAttribute: 'name'
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->native(false),

                DatePicker::make('checkin_date')
                    ->label('Tanggal Check-in')
                    ->required(),

                DatePicker::make('checkout_date')
                    ->label('Tanggal Check-out')
                    ->required(),

                TextInput::make('total_price')
                    ->label('Total Harga')
                    ->numeric()
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ])
                    ->required()
                    ->native(false),
            ]);
    }
}
