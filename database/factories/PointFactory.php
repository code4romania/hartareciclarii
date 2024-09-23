<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Point\Source;
use App\Models\City;
use App\Models\Point;
use App\Models\PointGroup;
use App\Models\PointType;
use App\Models\ServiceType;
use App\Models\User;
use App\Services\Nominatim;
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
            'address' => fake()->streetAddress(),
            'business_name' => null,
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
            'source' => fake()->randomElement(Source::values()),
            'administered_by' => fake()->company(),
            'verified_at' => now(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verified_at' => null,
        ]);
    }

    public function withType(ServiceType $serviceType, PointType $pointType): static
    {
        return $this->state(fn (array $attributes) => [
            'business_name' => $serviceType->can_have_business_name ? fake()->company() : null,
            'service_type_id' => $serviceType->id,
            'point_type_id' => $pointType->id,
        ]);
    }

    public function withMaterials(Collection $materials): static
    {
        return $this->afterCreating(function (Point $point) use ($materials) {
            $point->materials()->attach($materials);
        });
    }

    public function inCity(City $city): static
    {
        $location = Nominatim::make()->locate($city->name, $city->county->name);

        if (blank($location)) {
            return $this->state(fn (array $attributes) => [
                'county_id' => $city->county_id,
                'city_id' => $city->id,

            ]);
        }

        return $this->state(fn (array $attributes) => [
            'county_id' => $city->county_id,
            'city_id' => $city->id,
            'location' => new SpatialPoint(
                fake()->latitude(min: $location->bounds[0], max: $location->bounds[1]),
                fake()->longitude(min: $location->bounds[2], max: $location->bounds[3])
            ),
        ]);
    }

    public function createdByUser(?User $user = null): static
    {
        return $this->afterCreating(function (Point $point) use ($user) {
            $point->contribution()->create([
                'user_id' => $user?->id || User::factory(),
            ]);
        });
    }

    public function inGroup(): static
    {
        return $this->state(fn (array $attributes) => [
            'point_group_id' => PointGroup::factory(),
        ]);
    }
}
