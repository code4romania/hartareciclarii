<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitPointRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = match ($this->step) {
            1 => $this->getFirstStepRules(),
            2 => $this->getSecondStepRules(),
            default => array_merge($this->getFirstStepRules(), $this->getSecondStepRules()),
        };

        return array_merge($rules, [
            'step' => ['required', 'integer', 'min:1'],
        ]);
    }

    public function messages(): array
    {
        return [
            'location.lat.*' => __('validation.location'),
            'location.lng.*' => __('validation.location'),
        ];
    }

    protected function getFirstStepRules(): array
    {
        return [
            'service_type' => ['required', 'exists:service_types,id'],
            'address' => ['required', 'string'],
            'location' => ['required', 'array'],
            'location.lat' => ['required', 'numeric', 'between:-90,90'],
            'location.lng' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    protected function getSecondStepRules(): array
    {
        return [
            'service_type' => ['required', 'exists:service_types,id'],
        ];
    }
}
