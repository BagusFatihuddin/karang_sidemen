<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'visitor_id' => Visitor::factory(),
            'guest_name' => fake()->name(),
            'guest_phone' => fake()->phoneNumber(),
            'guest_city' => fake()->city(),
            'destination_id' => Destination::factory(),
            'booking_date' => fake()->date(),
            'total_person' => fake()->numberBetween(1, 20),
            'notes' => fake()->optional()->sentence(),
            'status' => fake()->randomElement([
                'pending',
                'confirmed',
                'cancelled',
                'completed',
            ]),
            'recorded_by' => User::factory(),
        ];
    }
}