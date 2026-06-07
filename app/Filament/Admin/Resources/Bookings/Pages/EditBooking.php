<?php

namespace App\Filament\Admin\Resources\Bookings\Pages;

use App\Filament\Admin\Resources\Bookings\BookingResource;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeFill(
        array $data
    ): array {
        $data['booking_mode'] =
            filled($data['visitor_id'] ?? null)
                ? 'visitor'
                : 'guest';

        return $data;
    }

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {
        if (($data['booking_mode'] ?? 'visitor') === 'guest') {
            $data['visitor_id'] = null;
        } else {
            $data['guest_name'] = null;
            $data['guest_phone'] = null;
            $data['guest_city'] = null;
        }

        unset($data['booking_mode']);

        return $data;
    }
}
