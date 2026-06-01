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
        'visitor_name',
        'rating',
        'review',
        'is_approved',
        'is_pinned',
        'is_pinned_homepage',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'is_pinned' => 'boolean',
            'is_pinned_homepage' => 'boolean',
        ];
    }

    /**
     * Review token relation.
     */
    public function reviewToken(): BelongsTo
    {
        return $this->belongsTo(ReviewToken::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ReviewFactory
    {
        return ReviewFactory::new();
    }
}