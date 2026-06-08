<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'facilities' => $this->facilities,
            'entry_fee' => $this->entry_fee,
            'parking_fee' => $this->parking_fee,
            'rental_price' => $this->rental_price,
            'destination_type' => $this->destination_type,
            'whatsapp_number' => $this->whatsapp_number,
            'maps_url' => $this->maps_url,
            'thumbnail_url' => $this->thumbnail_url,
            'images' => $this->when(
                $this->relationLoaded('images'),
                fn() => $this->images->map(fn($image) => [
                    'url' => $image->url,
                    'sort_order' => $image->sort_order,
                ])
            ),
            'total_visitors' => $this->when(
                $this->relationLoaded('dailyVisits'),
                fn() => (int) $this->dailyVisits->sum('visitor_count')
            ),
        ];
    }
}
