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
        debug($this->bounds);
        if (! \is_string($this->bounds)) {
            dd($this->bounds);
        } // (1)

        $this->merge([
            'bounds_asdadas' => explode(',', $this->bounds),
        ]);
    }

    public function rules(): array
    {
        return [

            'bounds_asdadas' => ['nullable', 'array', 'size:4'],
            'bounds_asdadas.*' => ['numeric', 'between:-180,180'],
            'search' => 'nullable|string',
        ];
    }

    protected function passedValidation(): void
    {
        if (! $this->bounds) {
            return;
        }
        $this->merge([
            'bounds' => $this->makePolygon($this->bounds_asdadas),
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
