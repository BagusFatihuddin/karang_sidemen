<?php

namespace App\Filament\Admin\Resources\Bookings\Infolists;

use App\Models\Booking;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Booking')
                    ->schema([
                        TextEntry::make('booking_code')
                            ->label('Kode Booking')
                            ->copyable()
                            ->copyMessage('Kode booking disalin.'),

                        TextEntry::make('guest_name')
                            ->label('Wisatawan')
                            ->state(
                                fn (Booking $record): string =>
                                $record->visitor?->name
                                    ?? $record->guest_name
                                    ?? '-'
                            )
                            ->placeholder('-'),

                        TextEntry::make('destination.name')
                            ->label('Destinasi')
                            ->placeholder('-'),

                        TextEntry::make('checkin_date')
                            ->label('Tanggal Check-in')
                            ->date('d M Y'),

                        TextEntry::make('checkout_date')
                            ->label('Tanggal Check-out')
                            ->date('d M Y'),

                        TextEntry::make('total_price')
                            ->label('Total Harga')
                            ->placeholder('-'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(
                                fn (?string $state): string => match ($state) {
                                    'pending' => 'Pending',
                                    'confirmed' => 'Confirmed',
                                    'cancelled' => 'Cancelled',
                                    'completed' => 'Completed',
                                    default => $state ?? '-',
                                }
                            ),

                        TextEntry::make('createdBy.name')
                            ->label('Dibuat Oleh')
                            ->placeholder('-'),

                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
