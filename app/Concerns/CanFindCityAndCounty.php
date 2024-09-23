<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\City;

trait CanFindCityAndCounty
{
    public function findCityAndCounty(): void
    {
        if (blank($this->city) || blank($this->county)) {
            return;
        }

        $city = City::search((string) $this->city)
            ->where('county', (string) $this->county)
            ->first();

        if (blank($city)) {
            return;
        }

        $this->merge([
            'city_id' => $city->id,
            'county_id' => $city->county_id,
        ]);
    }
}
