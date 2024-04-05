<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Point\ServiceType;
use App\Enums\Point\Status;
use App\Models\City;
use App\Models\Material;
use App\Models\Point;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $latitudeRange = [46.0, 46.1];
        $longitudeRange = [25.0, 25.1];
        $serviceType = $this->faker->randomElement(ServiceType::values());
        $serviceTypeEnum = ServiceType::from($serviceType);
        $city = City::query()->inRandomOrder()->first();

        return [
            'latitude' => $this->faker->randomFloat(6, $latitudeRange[0], $latitudeRange[1]),
            'longitude' => $this->faker->randomFloat(6, $longitudeRange[0], $longitudeRange[1]),
            'county_id' => $city->county_id,
            'city_id' => $city->id,
            'address' => $this->faker->address,
            'name' => 'Point-' . $this->faker->unique()->numberBetween(1, 100),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'website' => $this->faker->url,
            'notes' => $this->faker->sentence,
            'observations' => $this->faker->sentence,
            'schedule' => [
                'monday' => [
                    'start' => '08:00',
                    'end' => '16:00',
                ],
                'tuesday' => [
                    'start' => '08:00',
                    'end' => '16:00',
                ],
                'wednesday' => [
                    'start' => '08:00',
                    'end' => '16:00',
                ],
                'thursday' => [
                    'start' => '08:00',
                    'end' => '16:00',
                ],
                'friday' => [
                    'start' => '08:00',
                    'end' => '16:00',
                ],
                'saturday' => [
                    'start' => '08:00',
                    'end' => '16:00',
                ],
                'sunday' => [
                    'start' => '08:00',
                    'end' => '16:00',
                ],
            ],
            'offers_money' => $this->faker->boolean,
            'offers_vouchers' => $this->faker->boolean,
            'offers_transport' => $this->faker->boolean,
            'free_of_charge' => $this->faker->boolean,
            'status' => $this->faker->randomElement(Status::values()),
            'service_type' => $serviceType,
            'point_type' => $this->faker->randomElement($serviceTypeEnum->pointTypes()::values()),
            'source' => 'manual',
            'administered_by' => $this->faker->company,

        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Point $point) {
            $point->materials()->attach(Material::query()->inRandomOrder()->limit(3)->get());
        });
    }
}
