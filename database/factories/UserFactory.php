<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'anggota_pokdarwis',
            'is_active' => true,
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'role' => 'super_admin',
        ]);
    }

    public function adminKonten(): static
    {
        return $this->state(fn () => [
            'role' => 'admin_konten',
        ]);
    }

    public function pimpinan(): static
    {
        return $this->state(fn () => [
            'role' => 'pimpinan',
        ]);
    }

    public function anggotaPokdarwis(): static
    {
        return $this->state(fn () => [
            'role' => 'anggota_pokdarwis',
        ]);
    }

    public function petugasLapangan(): static
    {
        return $this->state(fn () => [
            'role' => 'petugas_lapangan',
        ]);
    }
}