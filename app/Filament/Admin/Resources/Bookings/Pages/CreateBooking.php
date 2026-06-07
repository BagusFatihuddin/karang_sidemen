<?php

namespace App\Filament\Admin\Resources\Bookings\Pages;

use App\Filament\Admin\Resources\Bookings\BookingResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeCreate(
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

        $data['created_by'] = Auth::id();

        return $data;
    }
}
