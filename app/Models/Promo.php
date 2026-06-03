<?php

namespace App\Models;

use Database\Factories\PromoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    /** @use HasFactory<PromoFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'image_public_id',
        'external_url',
        'is_active',
        'start_date',
        'end_date',
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
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Scope only active promos.
     */
    public function scopeActive(
        Builder $query
    ): void {
        $today = today();

        $query
            ->where('is_active', true)
            ->where(function (
                Builder $query
            ) use ($today) {
                $query
                    ->whereNull('start_date')
                    ->orWhere(
                        'start_date',
                        '<=',
                        $today
                    );
            })
            ->where(function (
                Builder $query
            ) use ($today) {
                $query
                    ->whereNull('end_date')
                    ->orWhere(
                        'end_date',
                        '>=',
                        $today
                    );
            });
    }

    /**
     * Create a new factory instance.
     */
    protected static function newFactory(): PromoFactory
    {
        return PromoFactory::new();
    }
}