<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\ReviewToken;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReviewToken>
 */
class ReviewTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ReviewToken>
     */
    protected $model = ReviewToken::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'token' => ReviewToken::generateToken(),

            'visitor_id' => Visitor::factory(),

            'destination_id' => Destination::factory(),

            'generated_by' => User::factory(),

            'is_used' => false,

            'expires_at' => ReviewToken::generateExpiry(),

            'used_at' => null,

            'created_at' => now(),
        ];
    }
}