<?php

namespace App\Models;

use Database\Factories\GuideFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guide extends Model
{
    /** @use HasFactory<GuideFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'bio',
        'experience',
        'photo_url',
        'photo_public_id',
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
        ];
    }

    /**
     * Guide trip packages relation.
     */
    public function tripPackages(): BelongsToMany
    {
        return $this->belongsToMany(
            TripPackage::class
        );
    }

    /**
     * Create a new factory instance.
     */
    protected static function newFactory(): GuideFactory
    {
        return GuideFactory::new();
    }
}