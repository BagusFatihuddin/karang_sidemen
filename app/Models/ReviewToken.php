<?php

namespace App\Models;

use Database\Factories\ReviewTokenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use RuntimeException;

class ReviewToken extends Model
{
    /** @use HasFactory<ReviewTokenFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'token',
        'visitor_id',
        'destination_id',
        'generated_by',
        'is_used',
        'expires_at',
        'used_at',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_used' => 'boolean',
            'used_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Review token visitor relation.
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    /**
     * Review token destination relation.
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /**
     * User who generated this review token.
     */
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Review relation.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Generate secure unique token.
     *
     * @throws RuntimeException
     */
    public static function generateToken(): string
    {
        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $token = bin2hex(random_bytes(32));

            $exists = static::query()
                ->where('token', $token)
                ->exists();

            if (! $exists) {
                return $token;
            }
        }

        throw new RuntimeException(
            'Failed to generate unique review token after 10 attempts.'
        );
    }

    /**
     * Generate token expiry.
     */
    public static function generateExpiry()
    {
        return now()->addDays(7);
    }

    /**
     * Determine whether token is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ReviewTokenFactory
    {
        return ReviewTokenFactory::new();
    }
}