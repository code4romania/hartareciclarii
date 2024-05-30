<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class MapRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        if (! $this->bounds) {
            return;
        }
        if (! $this->center) {
            return;
        }

        $this->merge([
            'bounds' => explode(',', $this->bounds),
            'center' => explode(',', $this->center),
        ]);
    }

    public function rules(): array
    {
        return [

            'bounds' => ['nullable', 'array', 'size:4'],
            'bounds.*' => ['numeric', 'between:-180,180'],
            'search' => 'nullable|string',
            'center' => ['nullable', 'array', 'size:2'],
            'from' =>['nullable', 'string'],
        ];
    }

    protected function passedValidation(): void
    {
        if (! $this->bounds) {
            return;
        }
        if (! $this->center) {
            return;
        }
        $this->merge([
            'bounds' => $this->makePolygon($this->bounds),
            'center' => new Point((float) $this->center[0], (float) $this->center[1]),
        ]);
    }

    private function makePolygon(array $bounds): ?Polygon
    {
        return new Polygon([
            new LineString([
                new Point((float) $bounds[3], (float) $bounds[2]),
                new Point((float) $bounds[3], (float) $bounds[0]),
                new Point((float) $bounds[1], (float) $bounds[0]),
                new Point((float) $bounds[1], (float) $bounds[2]),
                new Point((float) $bounds[3], (float) $bounds[2]),
            ]),
        ]);
    }
}
