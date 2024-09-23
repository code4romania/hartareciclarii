<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Concerns\CanFindCityAndCounty;
use App\Models\Problem\ProblemType;
use App\Models\TemporaryUpload;
use App\Rules\MaterialsAssociatedWithPoint;
use App\Rules\MaterialsMissingFromPoint;
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

    public ProblemType $problemType;

    public function rules(): array
    {
        $this->problemTypes = ProblemType::query()
            ->whereNull('parent_id')
            ->whereValidForServiceTypeId($this->point->service_type_id)
            ->with('children')
            ->get();

        $this->problemType = $this->problemTypes->firstWhere('id', $this->type_id);

        $rules = [
            // Type
            'type_id' => ['required', Rule::in($this->problemTypes->pluck('id'))],
        ];

        return array_merge($rules, match ($this->problemType?->slug) {
            'address' => $this->getRulesForAddress(),
            'location' => $this->getRulesForLocation(),
            'materials' => $this->getRulesForMaterials(),
            'rejected_waste', 'rejected_repair' => $this->getRulesForRejected(),
            'schedule', 'container', 'other' => $this->getRulesForNarrative(),
            default => [],
        });
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

    protected function getRulesForMaterials(): array
    {
        $pointMaterialIds = $this->point
            ->materials
            ->pluck('id');

        $selectedSubTypes = $this->problemType
            ->children
            ->filter(fn (ProblemType $subType) => \in_array($subType->id, $this->sub_types))
            ->pluck('slug');

        return [
            'materials_add' => [
                'bail',
                'array',
                Rule::requiredIf(fn () => $selectedSubTypes->contains('materials_add')),
                Rule::prohibitedIf(fn () => ! $selectedSubTypes->contains('materials_add')),
                new MaterialsMissingFromPoint($pointMaterialIds),
            ],
            'materials_remove' => [
                'bail',
                'array',
                Rule::requiredIf(fn () => $selectedSubTypes->contains('materials_remove')),
                Rule::prohibitedIf(fn () => ! $selectedSubTypes->contains('materials_remove')),
                new MaterialsAssociatedWithPoint($pointMaterialIds),
            ],
            'description' => [
                Rule::requiredIf(fn () => $selectedSubTypes->contains('materials_other')),
                Rule::prohibitedIf(fn () => ! $selectedSubTypes->contains('materials_other')),
                'max:1000',
            ],

            ...$this->getRulesForSubTypes(),
            ...$this->getRulesForImages(),
        ];
    }

    protected function getRulesForRejected(): array
    {
        return [
            ...$this->getRulesForDescription(),
            ...$this->getRulesForSubTypes(),
        ];
    }

    protected function getRulesForNarrative(): array
    {
        return [
            ...$this->getRulesForDescription(),
            ...$this->getRulesForImages(),
        ];
    }

    private function getRulesForDescription(): array
    {
        return [
            'description' => ['required', 'string', 'max:1000'],
        ];
    }

    private function getRulesForImages(): array
    {
        return [
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['required', Rule::exists('media', 'uuid')->where('model_type', app(TemporaryUpload::class)->getMorphClass())],
        ];
    }

    private function getRulesForSubTypes(): array
    {
        if ($this->problemType->children->isEmpty()) {
            return [];
        }

        return [
            'sub_types' => ['required', 'array', 'min:1'],
            'sub_types.*' => ['required', Rule::in($this->problemType->children->pluck('id'))],
        ];
    }
}
