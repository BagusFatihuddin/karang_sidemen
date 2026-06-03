<?php

namespace App\Models;

use Database\Factories\TripPackageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TripPackage extends Model
{
    /** @use HasFactory<TripPackageFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'image_public_id',
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
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Package destinations relation.
     */
    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(
            Destination::class
        )
        ->withPivot('sort_order');
    }

    /**
     * Package guides relation.
     */
    public function guides(): BelongsToMany
    {
        return $this->belongsToMany(
            Guide::class
        );
    }

    /**
     * Create a new factory instance.
     */
    protected static function newFactory(): TripPackageFactory
    {
        return TripPackageFactory::new();
    }
}