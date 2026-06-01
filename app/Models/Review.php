<?php

namespace App\Models;

use Database\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    /** @use HasFactory<ReviewFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'review_token_id',
        'visitor_id',
        'destination_id',
        'reviewer_name',
        'reviewer_city',
        'rating',
        'review_text',
        'photo_url',
        'photo_public_id',
        'status',
        'is_pinned_destination',
        'is_pinned_global',
        'approved_by',
        'approved_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_pinned_destination' => 'boolean',
            'is_pinned_global' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }


    /**
 * Boot model events.
 */
protected static function booted(): void
{
    static::creating(function (Review $review) {
        if (blank($review->status)) {
            throw new \InvalidArgumentException(
                'Review status is required.'
            );
        }
    });
}

    /**
     * Review token relation.
     */
    public function reviewToken(): BelongsTo
    {
        return $this->belongsTo(ReviewToken::class);
    }

    /**
     * Review visitor relation.
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    /**
     * Review destination relation.
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /**
     * Admin who approved review.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ReviewFactory
    {
        return ReviewFactory::new();
    }
}