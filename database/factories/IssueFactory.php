<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\IssueStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Issue>
 */
class IssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reporterId = null;
        if ($this->faker->boolean) {
            $reporterId = User::factory();
        }

        return [
            'point_id' => null,
            'service_type_id' => null,
            'user_id' => $reporterId,
            'status' => $this->faker->randomElement(IssueStatus::values()),
            'description' => $this->faker->sentence,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
