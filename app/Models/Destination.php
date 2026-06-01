<?php

namespace App\Models;

use Database\Factories\DestinationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Destination extends Model
{
    /** @use HasFactory<DestinationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'facilities',
        'entry_fee',
        'parking_fee',
        'rental_price',
        'destination_type',
        'whatsapp_number',
        'maps_url',
        'cloudinary_folder',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'entry_fee' => 'decimal:2',
            'parking_fee' => 'decimal:2',
            'rental_price' => 'decimal:2',
        ];
    }

    /**
     * Destination images relation.
     */
    public function images(): HasMany
    {
        return $this->hasMany(DestinationImage::class);
    }

        /**
     * Destination visitors relation.
     */
    public function visitors(): HasMany
    {
        return $this->hasMany(Visitor::class);
    }

    /**
     * Destination daily visits relation.
     */
    public function dailyVisits(): HasMany
    {
        return $this->hasMany(DailyVisit::class);
    }

        /**
     * Destination bookings relation.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }


/**
 * Destination review tokens relation.
 */
public function reviewTokens(): HasMany
{
    return $this->hasMany(ReviewToken::class);
}

/**
 * Destination reviews relation.
 */
public function reviews(): HasMany
{
    return $this->hasMany(Review::class);
}


    /**
     * Scope only active destinations.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): DestinationFactory
    {
        return DestinationFactory::new();
    }
}