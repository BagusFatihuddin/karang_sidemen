<?php

namespace App\Models;

use Database\Factories\VisitorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Visitor extends Model
{
    /** @use HasFactory<VisitorFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'whatsapp_number',
        'origin_category',
        'origin_city',
        'visit_type',
        'group_size',
        'referral_source',
        'referral_other',
        'destination_id',
        'recorded_by',
        'visited_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
        ];
    }

    /**
     * Visitor destination relation.
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /**
     * User who recorded this visitor.
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

        /**
     * Visitor bookings relation.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

/**
 * Visitor review tokens relation.
 */
public function reviewTokens(): HasMany
{
    return $this->hasMany(ReviewToken::class);
}

/**
 * Visitor reviews relation.
 */
public function reviews(): HasMany
{
    return $this->hasMany(Review::class);
}

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): VisitorFactory
    {
        return VisitorFactory::new();
    }
}