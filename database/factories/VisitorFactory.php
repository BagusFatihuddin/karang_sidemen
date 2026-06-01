<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visitor>
 */
class VisitorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Visitor>
     */
    protected $model = Visitor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'whatsapp_number' => fake()->phoneNumber(),

            'origin_category' => fake()->randomElement([
                'lombok_tengah',
                'lombok_lainnya',
                'luar_lombok',
                'mancanegara',
            ]),

            'origin_city' => fake()->city(),

            'visit_type' => fake()->randomElement([
                'sendiri',
                'pasangan',
                'keluarga',
                'rombongan',
            ]),

            'group_size' => fake()->numberBetween(1, 20),

            'referral_source' => fake()->randomElement([
                'instagram',
                'whatsapp',
                'teman',
                'google',
                'lainnya',
            ]),

            'referral_other' => fake()->optional()->word(),

            'destination_id' => Destination::factory(),
            'recorded_by' => User::factory(),

            'visited_at' => fake()->optional()->dateTime(),
        ];
    }
}