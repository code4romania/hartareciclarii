<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Point\Source;
use App\Models\Point;
use App\Models\PointGroup;
use App\Models\Problem\Problem;
use App\Models\ServiceType;
use App\Models\User;
use App\Services\Nominatim;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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

    public function withType(ServiceType $serviceType): static
    {
        return $this->state(fn (array $attributes) => [
            'business_name' => $serviceType->can_have_business_name ? fake()->company() : null,
            'service_type_id' => $serviceType->id,
            'point_type_id' => $serviceType->pointTypes->random()->id,
        ]);
    }

    public function withMaterials(Collection $materials): static
    {
        return $this->afterCreating(function (Point $point) use ($materials) {
            if (! $point->serviceType->can_collect_materials) {
                return;
            }

            $point->materials()->attach($materials->random(3));
        });
    }

    public function inRandomCity(Collection $cities): static
    {
        return $this->state(function (array $attributes) use ($cities) {
            $city = $cities->random();

            $location = Cache::driver('array')
                ->rememberForever(
                    "factory-location-$city->id",
                    fn () => Nominatim::make()->locate($city->name, $city->county->name)
                );

            return[
                'county_id' => $city->county_id,
                'city_id' => $city->id,
                'location' => blank($location)
                    ? $attributes['location']
                    : new SpatialPoint(
                        fake()->latitude(min: $location->bounds[0], max: $location->bounds[1]),
                        fake()->longitude(min: $location->bounds[2], max: $location->bounds[3])
                    ),
            ];
        });
    }

    public function createdByUser(?User $user = null): static
    {
        return $this->afterCreating(function (Point $point) use ($user) {
            $point->contribution()->create([
                'user_id' => $user?->id || User::factory(),
            ]);
        });
    }

    public function withProblems(Collection $problemTypes, Collection $users): static
    {
        return $this->afterCreating(function (Point $point) use ($problemTypes, $users) {
            if ($problemTypes->isEmpty()) {
                return;
            }

            $count = fake()->numberBetween(1, $problemTypes->count());

            Problem::factory()
                ->count($count)
                ->sequence(fn () => [
                    'type_id' => $problemTypes->random()->id,
                ])
                ->for($point)
                ->createdByUser($users->random())
                ->create();
        });
    }

    public function inGroup(): static
    {
        return $this->state(fn (array $attributes) => [
            'point_group_id' => PointGroup::factory(),
        ]);
    }
}
