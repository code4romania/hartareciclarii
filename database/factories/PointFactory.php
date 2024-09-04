<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Point\Source;
use App\Enums\Point\Status;
use App\Models\City;
use App\Models\Point;
use App\Models\PointGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use MatanYadaev\EloquentSpatial\Objects\Point as SpatialPoint;

/**
 * @extends Factory<\App\Models\Point>
 */
class PointFactory extends Factory
{
    protected $model = Point::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location' => new SpatialPoint(
                fake()->latitude(min: 43.66, max: 48.21),
                fake()->longitude(min: 20.29, max: 29.61)
            ),
            'address' => fake()->address(),
            'business_name' => fake()->boolean() ? fake()->company() : null,
            'phone' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'website' => fake()->url(),
            'notes' => fake()->sentence(),
            'observations' => fake()->sentence(),
            'schedule' => 'Monday to Friday, 9am to 5pm',
            'offers_money' => fake()->boolean(),
            'offers_vouchers' => fake()->boolean(),
            'offers_transport' => fake()->boolean(),
            'free_of_charge' => fake()->boolean(),
            'status' => fake()->randomElement(Status::values()),
            'source' => fake()->randomElement(Source::values()),
            'administered_by' => fake()->company(),
        ];
    }

    public function withMaterials(Collection $materials): static
    {
        return $this->afterCreating(function (Point $point) use ($materials) {
            $point->materials()->attach($materials);
        });
    }

    public function inCity(City $city): static
    {
        return $this->state(fn (array $attributes) => [
            'county_id' => $city->county_id,
            'city_id' => $city->id,
        ]);
    }

    public function inLisbon(): static
    {
        $latitudeRange = [38.7363163, 38.7408642];
        $longitudeRange = [-9.1353215, -9.1325596];

        return $this->state(fn (array $attributes) => [
            'location' => new SpatialPoint(
                fake()->latitude(min: 38.80, max: 38.70),
                fake()->longitude(min: -9.18, max: -9.10)
            ),
        ]);
    }

    public function createdByUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => User::factory(),
        ]);
    }

    public function inGroup(): static
    {
        return $this->state(fn (array $attributes) => [
            'point_group_id' => PointGroup::factory(),
        ]);
    }
}
