<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\TripPackage;
use Illuminate\Http\JsonResponse;

class TripPackageController extends Controller
{
    public function index(): JsonResponse
    {
        $tripPackages = TripPackage::query()
            ->where('is_active', true)
            ->with([
                'destinations' => fn ($query) => $query
                    ->orderBy('sort_order')
                    ->select([
                        'destinations.id',
                        'name',
                        'destination_type',
                        'entry_fee',
                        'parking_fee',
                        'rental_price',
                        'whatsapp_number',
                        'maps_url',
                    ]),
                'guides' => fn ($query) => $query
                    ->select([
                        'guides.id',
                        'name',
                        'bio',
                        'experience',
                        'photo_url',
                    ]),
            ])
            ->latest()
            ->get([
                'id',
                'name',
                'description',
                'price',
                'image_url',
            ]);

        return ApiResponse::success(
            $tripPackages->map(
                fn (TripPackage $tripPackage): array =>
                    $this->formatTripPackage($tripPackage)
            )
        );
    }

    public function show(int $id): JsonResponse
    {
        $tripPackage = TripPackage::query()
            ->where('is_active', true)
            ->with([
                'destinations' => fn ($query) => $query
                    ->orderBy('sort_order')
                    ->select([
                        'destinations.id',
                        'name',
                        'destination_type',
                        'entry_fee',
                        'parking_fee',
                        'rental_price',
                        'whatsapp_number',
                        'maps_url',
                    ]),
                'guides' => fn ($query) => $query
                    ->select([
                        'guides.id',
                        'name',
                        'bio',
                        'experience',
                        'photo_url',
                    ]),
            ])
            ->find($id);

        if (! $tripPackage) {
            return ApiResponse::error(
                'Trip package tidak ditemukan.',
                404
            );
        }

        return ApiResponse::success(
            $this->formatTripPackage($tripPackage)
        );
    }

    protected function formatTripPackage(
        TripPackage $tripPackage
    ): array {
        return [
            'id' => $tripPackage->id,
            'name' => $tripPackage->name,
            'description' => $tripPackage->description,
            'price' => $tripPackage->price,
            'image_url' => $tripPackage->image_url,
            'destinations' => $tripPackage->destinations
                ->map(
                    fn ($destination): array => [
                        'id' => $destination->id,
                        'name' => $destination->name,
                        'destination_type' => $destination->destination_type,
                        'entry_fee' => $destination->entry_fee,
                        'parking_fee' => $destination->parking_fee,
                        'rental_price' => $destination->rental_price,
                        'whatsapp_number' => $destination->whatsapp_number,
                        'maps_url' => $destination->maps_url,
                    ]
                )
                ->values(),
            'guides' => $tripPackage->guides
                ->map(
                    fn ($guide): array => [
                        'id' => $guide->id,
                        'name' => $guide->name,
                        'bio' => $guide->bio,
                        'experience' => $guide->experience,
                        'photo_url' => $guide->photo_url,
                    ]
                )
                ->values(),
        ];
    }
}
