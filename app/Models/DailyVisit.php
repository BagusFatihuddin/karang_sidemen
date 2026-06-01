<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyVisit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'destination_id',
        'date',
        'visitor_count',
        'revenue',
        'expense',
        'recorded_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'revenue' => 'decimal:2',
            'expense' => 'decimal:2',
        ];
    }

    /**
     * Daily visit destination relation.
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /**
     * User who recorded this daily visit.
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Create or update daily visit by date.
     *
     * @param array<string, mixed> $values
     */
    public static function updateOrCreateForDate(
        int $destinationId,
        string $date,
        array $values
    ): self {
        return self::updateOrCreate(
            [
                'destination_id' => $destinationId,
                'date' => $date,
            ],
            $values
        );
    }
}