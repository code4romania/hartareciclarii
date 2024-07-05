<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\IssueStatus;
use App\Models\Point;
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
        $point = Point::factory()->create();

        $type = $this->faker->randomElement($point->service_type->issueTypes()::values());
        $fields = $point->service_type->issueFields($type);

        return [
            'point_id' => $point->id,
            'user_id' => $reporterId,
            'status' => $this->faker->randomElement(IssueStatus::values()),
            'type' => $point->service_type->value,
            'type_value' => $type,
            'issues' => $fields,
            'description' => $this->faker->sentence,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),

        ];
    }
}
