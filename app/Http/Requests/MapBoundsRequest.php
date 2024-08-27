<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class MapBoundsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'bounds' => ['nullable', 'array', 'size:4'],
            'bounds.*' => ['numeric', 'between:-180,180'],

            'center' => ['nullable', 'array', 'size:2'],
            'center.*' => ['numeric', 'between:-180,180'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->bounds) {
            $this->replace([
                'bounds' => explode(',', $this->bounds),
            ]);
        }

        if ($this->center) {
            $this->replace([
                'center' => explode(',', $this->center),
            ]);
        }
    }

    protected function passedValidation(): void
    {
        if ($this->bounds) {
            $this->replace([
                'bounds' => $this->makePolygon($this->bounds),
            ]);
        }

        if ($this->center) {
            $this->replace([
                'center' => $this->makePoint($this->center),
            ]);
        }
    }

    private function makePolygon(array $bounds): Polygon
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

    private function makePoint(array $center): Point
    {
        return new Point(
            (float) $center[0],
            (float) $center[1]
        );
    }
}
