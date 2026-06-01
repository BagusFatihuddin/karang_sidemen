<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\ReviewToken;
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

            'visitor_name' => fake()->name(),

            'rating' => fake()->numberBetween(1, 5),

            'review' => fake()->paragraph(),

            'is_approved' => false,
            'is_pinned' => false,
            'is_pinned_homepage' => false,
        ];
    }
}