<?php

namespace App\Filament\Admin\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('booking_code')
                    ->label('Kode Booking')
                    ->disabled()
                    ->dehydrated(false)
                    ->copyable(
                        copyMessage: 'Kode booking disalin.'
                    )
                    ->helperText('Otomatis dibuat sistem')
                    ->visible(
                        fn (string $operation): bool =>
                        $operation === 'edit'
                    ),

                Select::make('booking_mode')
                    ->label('Mode Booking')
                    ->options([
                        'visitor' => 'Wisatawan Terdaftar',
                        'guest' => 'Input Manual',
                    ])
                    ->default('visitor')
                    ->live()
                    ->afterStateUpdated(function (
                        Set $set,
                        ?string $state
                    ): void {
                        if ($state === 'visitor') {
                            $set('guest_name', null);
                            $set('guest_phone', null);

                            return;
                        }

                        $set('visitor_id', null);
                    })
                    ->required()
                    ->native(false),

                Select::make('visitor_id')
                    ->label('Wisatawan')
                    ->relationship(
                        name: 'visitor',
                        titleAttribute: 'name'
                    )
                    ->searchable()
                    ->preload()
                    ->required(
                        fn (Get $get): bool =>
                        $get('booking_mode') === 'visitor'
                    )
                    ->visible(
                        fn (Get $get): bool =>
                        $get('booking_mode') === 'visitor'
                    )
                    ->native(false),

                TextInput::make('guest_name')
                    ->label('Nama Guest')
                    ->required(
                        fn (Get $get): bool =>
                        $get('booking_mode') === 'guest'
                    )
                    ->visible(
                        fn (Get $get): bool =>
                        $get('booking_mode') === 'guest'
                    )
                    ->maxLength(100),

                TextInput::make('guest_phone')
                    ->label('WhatsApp Guest')
                    ->tel()
                    ->required(
                        fn (Get $get): bool =>
                        $get('booking_mode') === 'guest'
                    )
                    ->visible(
                        fn (Get $get): bool =>
                        $get('booking_mode') === 'guest'
                    )
                    ->maxLength(20)
                    ->rule(
                        'regex:/^(\+628|628|08)[0-9]{8,12}$/'
                    ),

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
                    ->minValue(0)
                    ->maxValue(9999999999.99)
                    ->rule('regex:/^\d+(\.\d{1,2})?$/')
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
