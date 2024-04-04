<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname' => fake()->firstname(),
            'lastname' => fake()->lastname(),
            'accept_terms' => fake()->boolean(),
            'send_newsletter' => fake()->boolean(),
            'email_confirmed' => fake()->boolean(),
            'last_login_ip' => ip2long(fake()->ipv4()),
            'email' => fake()->unique()->safeEmail(),
            'last_login_date' => now(),
            'password' => '$2y$12$Z/vhVO3e.UXKaG11EWgxc.EL7uej3Pi1M0Pq0orF5cbFGtyVh0V3C', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_confirmed' => 0,
        ]);
    }
}
