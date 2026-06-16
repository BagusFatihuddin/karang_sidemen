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
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'facilities' => $this->facilities,
            'tourism_vibe' => $this->tourism_vibe,
            'tags' => $this->asList($this->tags),
            'highlights' => $this->asList($this->highlights),
            'activity_keywords' => $this->asList($this->activity_keywords),
            'source_urls' => $this->asList($this->source_urls),
            'is_featured_homepage' => $this->is_featured_homepage,
            'homepage_sort_order' => $this->homepage_sort_order,
            'homepage_label' => $this->homepage_label,
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

    /**
     * Normalize legacy string values and current array values for public API.
     *
     * @return array<int, mixed>
     */
    private function asList(mixed $value): array
    {
        if (blank($value)) {
            return [];
        }

        if (is_array($value)) {
            return array_values(array_filter($value, fn ($item) => filled($item)));
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                return array_values(array_filter($decoded, fn ($item) => filled($item)));
            }

            return array_values(
                array_filter(
                    array_map('trim', explode(',', $value)),
                    fn ($item) => filled($item)
                )
            );
        }

        return [];
    }
}
