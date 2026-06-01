<?php

namespace Database\Factories;

use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Destination>
 */
class DestinationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Destination>
     */
    protected $model = Destination::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraphs(3, true),
            'facilities' => fake()->optional()->sentence(),

            'entry_fee' => fake()->optional()->randomFloat(2, 5000, 50000),
            'parking_fee' => fake()->optional()->randomFloat(2, 2000, 10000),
            'rental_price' => fake()->optional()->randomFloat(2, 10000, 150000),

            'destination_type' => fake()->randomElement([
                'camping',
                'air',
                'edukasi',
                'alam',
                'kuliner',
                'lainnya',
            ]),

            'whatsapp_number' => fake()->optional()->phoneNumber(),
            'maps_url' => fake()->optional()->url(),
            'cloudinary_folder' => fake()->optional()->slug(),

            'is_active' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that destination is active.
     */
    public function active(): static
    {
        return $this->state(fn () => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that destination is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}