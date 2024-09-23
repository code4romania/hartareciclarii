<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Point;
use App\Models\Problem\Problem;
use App\Models\Problem\ProblemType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Problem>
 */
class ProblemFactory extends Factory
{
    protected $model = Problem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'point_id' => Point::factory(),
            'address' => fake()->address(),
            'description' => fake()->sentence(),
            'started_at' => fake()->boolean(50) ? fake()->dateTime() : null,
            'closed_at' => fake()->boolean(25) ? fake()->dateTime() : null,
        ];
    }

    public function createdByUser(?User $user = null): static
    {
        return $this->afterCreating(function (Problem $problem) use ($user) {
            $problem->contribution()->create([
                'user_id' => $user?->id || User::factory(),
            ]);
        });
    }
}
