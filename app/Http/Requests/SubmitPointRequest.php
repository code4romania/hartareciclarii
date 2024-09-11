<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\City;
use App\Models\ServiceType;
use App\Models\TemporaryUpload;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class SubmitPointRequest extends FormRequest
{
    public function rules(): array
    {
        $serviceTypes = ServiceType::all();

        return [
            'step' => ['sometimes', 'string', 'in:type,details,materials,review'],

            // Step 1 & 2: Type & Location
            'service_type_id' => ['required', Rule::in($serviceTypes->pluck('id'))],
            'address' => ['required', 'string', 'max:100'],
            'city_id' => ['required', 'exists:cities,id'],
            'county_id' => ['required', 'exists:counties,id'],
            'location' => ['required', 'array', 'size:2'],
            'location.lat' => ['required', 'numeric', 'between:-90,90'],
            'location.lng' => ['required', 'numeric', 'between:-180,180'],

            // Step 3: Details
            'point_type_id' => ['required', 'exists:point_types,id'],
            'business_name' => ['nullable', 'string', 'max:50'],
            'administered_by' => ['exclude_if:administered_by_unknown,true', 'required_if:administered_by_unknown,false', 'nullable', 'string', 'max:50'],
            'administered_by_unknown' => ['boolean'],
            'schedule' => ['exclude_if:schedule_unknown,true', 'required_if:schedule_unknown,false', 'nullable', 'string', 'max:50'],
            'schedule_unknown' => ['boolean'],

            'offers_money' => ['nullable', Rule::excludeIf(fn () => $this->offers_money === -1), 'boolean'],
            'offers_vouchers' => ['nullable', Rule::excludeIf(fn () => $this->offers_vouchers === -1), 'boolean'],
            'offers_transport' => ['nullable', Rule::excludeIf(fn () => $this->offers_transport === -1), 'boolean'],
            'free_of_charge' => ['nullable', Rule::excludeIf(fn () => $this->free_of_charge === -1), 'boolean'],

            'website' => ['nullable', 'url', 'max:50'],
            'email' => ['nullable', 'email', 'max:50'],
            'phone' => ['nullable', 'numeric', 'max_digits:14'],
            'observations' => ['nullable', 'string', 'max:500'],

            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['required', Rule::exists('media', 'uuid')->where('model_type', app(TemporaryUpload::class)->getMorphClass())],

            // Step 4: Materials
            ...$this->getMaterialRules($serviceTypes),
        ];
    }

    public function messages(): array
    {
        return [
            'location.lat.*' => __('validation.location'),
            'location.lng.*' => __('validation.location'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->findCityAndCounty();
    }

    protected function getMaterialRules(Collection $serviceTypes): array
    {
        $canCollectMaterials = $serviceTypes
            ->where('can_collect_materials', true)
            ->pluck('id')
            ->contains($this->service_type_id);

        if (! $canCollectMaterials) {
            return [
                'materials' => ['exclude'],
                'materials.*' => ['exclude'],
            ];
        }

        return [
            'materials' => ['required', 'array', 'min:1'],
            'materials.*' => ['required', 'exists:materials,id'],
        ];
    }

    private function findCityAndCounty(): void
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
