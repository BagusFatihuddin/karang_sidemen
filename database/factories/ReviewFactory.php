<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Review;
use App\Models\ReviewToken;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Review>
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'review_token_id' => ReviewToken::factory(),

            'visitor_id' => Visitor::factory(),

            'destination_id' => Destination::factory(),

            'reviewer_name' => fake()->name(),

            'reviewer_city' => fake()->city(),

            'rating' => fake()->numberBetween(1, 5),

            'review_text' => fake()->optional()->paragraph(),

            'photo_url' => fake()->optional()->imageUrl(),

            'photo_public_id' => fake()->optional()->uuid(),

            'status' => fake()->randomElement([
                'pending',
                'approved',
                'rejected',
            ]),

            'is_pinned_destination' => false,

            'is_pinned_global' => false,

            'approved_by' => null,

            'approved_at' => null,
        ];
    }
}