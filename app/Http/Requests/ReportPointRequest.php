<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Concerns\CanFindCityAndCounty;
use App\Models\Problem\ProblemType;
use App\Models\TemporaryUpload;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ReportPointRequest extends FormRequest
{
    use CanFindCityAndCounty;

    protected $stopOnFirstFailure = true;

    public Collection $problemTypes;

    public function rules(): array
    {
        $this->problemTypes = ProblemType::query()
            ->whereNull('parent_id')
            ->whereValidForServiceTypeId($this->point->service_type_id)
            ->with('children')
            ->get();

        return [
            'step' => ['required', 'string'/* 'in:type,materials,review' */],

            // Type
            'type_id' => ['required', Rule::in($this->problemTypes->pluck('id'))],

            ...$this->getRulesForProblemType(),

        ];
    }

    protected function prepareForValidation(): void
    {
        $this->findCityAndCounty();
    }

    // public function after(): Closure
    // {
    //     return function (Validator $validator) {
    //         if ($this->isPrecognitive()) {
    //             return;
    //         }

    //         // dd('validator', $this->point->fill());

    //         if ($this->point->isClean()) {
    //             $validator->errors()->add(
    //                 'unchanged',
    //                 'You must change at least one field to update the point.'
    //             );
    //         }

    //         $this->point->discardChanges();

    //         // if ($this->somethingElseIsInvalid()) {
    //         //     $validator->errors()->add(
    //         //         'field',
    //         //         'Something is wrong with this field!'
    //         //     );
    //         // }
    //     };
    // }

    protected function getRulesForProblemType(): array
    {
        $problemType = $this->problemTypes->firstWhere('id', $this->type_id);

        return match ($problemType?->slug) {
            'address' => $this->getRulesForAddress(),
            'location' => $this->getRulesForLocation(),
            'schedule', 'container', 'other' => $this->getRulesForNarrative(),
            default => [],
        };
    }

    protected function getRulesForAddress(): array
    {
        return [
            'address' => ['required', 'string', 'max:100'],
            'city_id' => ['required', 'exists:cities,id'],
            'county_id' => ['required', 'exists:counties,id'],
            'location' => ['required', 'array', 'size:2'],
            'location.lat' => ['required', 'numeric', 'between:-90,90'],
            'location.lng' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    protected function getRulesForLocation(): array
    {
        return [
            'address' => ['required', 'string', 'max:100'],
            'city_id' => ['required', 'exists:cities,id'],
            'county_id' => ['required', 'exists:counties,id'],
            'location' => ['required', 'array', 'size:2'],
            'location.lat' => ['required', 'numeric', 'between:-90,90'],
            'location.lng' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    protected function getRulesForSchedule(): array
    {
        return [
            'description' => ['required', 'string', 'max:1000'],
        ];
    }

    protected function getRulesForNarrative(): array
    {
        return [
            'description' => ['required', 'string', 'max:1000'],
            ...$this->getRulesForImages(),
        ];
    }

    private function getRulesForImages(): array
    {
        return [
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['required', Rule::exists('media', 'uuid')->where('model_type', app(TemporaryUpload::class)->getMorphClass())],
        ];
    }
}
