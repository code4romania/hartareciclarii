<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Support\Facades\Validator;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class MapCoordinates
{
    private float $latitude = 45.9432;

    private float $longitude = 24.9668;

    private int $zoom = 10;

    private array $bounds = [];

    public function __construct(?string $coordinates = null)
    {
        $this->parseCoordinates($coordinates);

        $this->parseBounds(
            request()->header('Map-Bounds')
        );
    }

    /**
     * Attempt to parse the given coordinates. If the coordinates are invalid,
     * fallback to the default latitude, longitude, and zoom values.
     *
     * @param  null|string $coordinates
     * @return void
     */
    protected function parseCoordinates(?string $coordinates = null): void
    {
        preg_match('/^@([+-]?[0-9.]+),([+-]?[0-9.]+),([0-9]+)z/ui', (string) $coordinates, $matches);

        if (\count($matches) !== 4) {
            return;
        }

        $validator = Validator::make([
            'latitude' => $matches[1],
            'longitude' => $matches[2],
            'zoom' => $matches[3],
        ], [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'zoom' => ['required', 'integer', 'between:0,20'],
        ]);

        if ($validator->fails()) {
            return;
        }

        $validated = $validator->safe();

        $this->latitude = (float) $validated['latitude'];
        $this->longitude = (float) $validated['longitude'];
        $this->zoom = (int) $validated['zoom'];
    }

    /**
     * Attempt to parse the given bounds.
     *
     * @param  null|string $bounds
     * @return void
     */
    protected function parseBounds(?string $bounds = null): void
    {
        if (blank($bounds)) {
            return;
        }

        $bounds = explode(',', $bounds);

        $validator = Validator::make([
            'bounds' => $bounds,
        ], [
            'bounds' => ['required', 'array', 'size:4'],
            'bounds.*' => ['required', 'numeric', 'between:-180,180'],
        ]);

        if ($validator->fails()) {
            return;
        }

        $this->bounds = collect($validator->safe()['bounds'])
            ->map('floatval')
            ->all();
    }

    public function hasBounds(): bool
    {
        return filled($this->bounds);
    }

    public function getBounds(): Polygon
    {
        return new Polygon([
            new LineString([
                new Point($this->bounds[3], $this->bounds[2]),
                new Point($this->bounds[3], $this->bounds[0]),
                new Point($this->bounds[1], $this->bounds[0]),
                new Point($this->bounds[1], $this->bounds[2]),
                new Point($this->bounds[3], $this->bounds[2]),
            ]),
        ]);
    }

    public function getBoundsBBox(): ?string
    {
        if (blank($this->bounds)) {
            return null;
        }

        return implode(',', $this->bounds);
    }

    public function getCenter(): Point
    {
        return new Point($this->latitude, $this->longitude);
    }

    public function getZoom(): int
    {
        return $this->zoom;
    }
}
