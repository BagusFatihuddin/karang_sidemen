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
        $checkinDate = fake()->dateTimeBetween('now', '+30 days')
    ->format('Y-m-d');

        return [
            'visitor_id' => Visitor::factory(),

            'guest_name' => fake()->name(),
            'guest_phone' => fake()->phoneNumber(),
            'guest_city' => fake()->city(),

            'destination_id' => Destination::factory(),

            'checkin_date' => $checkinDate,

            'checkout_date' => fake()->optional()->passthrough(
                fake()->dateTimeBetween(
                    $checkinDate,
                    '+7 days'
                )->format('Y-m-d')
            ),

            'total_price' => fake()->optional()->randomFloat(
                2,
                50000,
                1000000
            ),

            'status' => fake()->randomElement([
                'pending',
                'confirmed',
                'cancelled',
                'completed',
            ]),

            'created_by' => User::factory(),

            'arrived_at' => fake()->optional()->dateTimeBetween(
                '-3 days',
                'now'
            ),
        ];
    }
}