<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'booking_code',
        'visitor_id',
        'guest_name',
        'guest_phone',
        'guest_city',
        'destination_id',
        'checkin_date',
        'checkout_date',
        'total_price',
        'status',
        'created_by',
        'arrived_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'checkin_date' => 'date',
            'checkout_date' => 'date',
            'total_price' => 'decimal:2',
            'arrived_at' => 'datetime',
        ];
    }

    /**
     * Boot model events.
     */
    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (blank($booking->booking_code)) {
                $booking->booking_code =
                    static::generateBookingCode();
            }
        });
    }

    /**
     * Generate unique booking code.
     *
     * Format: KS-XXXXX
     *
     * @throws RuntimeException
     */
    public static function generateBookingCode(): string
    {
        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $code = 'KS-' . str_pad(
                (string) random_int(0, 99999),
                5,
                '0',
                STR_PAD_LEFT
            );

            $exists = static::query()
                ->where('booking_code', $code)
                ->exists();

            if (! $exists) {
                return strtoupper($code);
            }
        }

        throw new RuntimeException(
            'Failed to generate unique booking code after 10 attempts.'
        );
    }

    /**
     * Booking visitor relation.
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    /**
     * Booking destination relation.
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /**
     * User who created this booking.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): BookingFactory
    {
        return BookingFactory::new();
    }
}