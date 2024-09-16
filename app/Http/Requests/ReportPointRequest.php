<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Problem\ProblemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportPointRequest extends FormRequest
{
    public function rules(): array
    {
        $problemTypes = ProblemType::query()
            ->whereValidForServiceTypeId($this->point->service_type_id)
            ->get();

        return [
            'step' => ['sometimes', 'string', 'in:type,materials,review'],

            // Type
            'problem_type_id' => ['required', Rule::in($problemTypes->pluck('id'))],

            // Location
            'address' => ['required', 'string', 'max:100'],
            'city_id' => ['required', 'exists:cities,id'],
            'county_id' => ['required', 'exists:counties,id'],
            'location' => ['required', 'array', 'size:2'],
            'location.lat' => ['required', 'numeric', 'between:-90,90'],
            'location.lng' => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}
