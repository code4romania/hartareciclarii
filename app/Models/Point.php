<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Point\ServiceType;
use App\Enums\Point\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitude',
        'longitude',
        'address',
        'name',
        'phone',
        'email',
        'website',
        'notes',
        'observations',
        'schedule',
        'status',
        'service_type',
        'point_type',

    ];

    protected $casts = [
        'schedule' => 'array',
        'status' => Status::class,
        'service_type' => ServiceType::class,
    ];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class);
    }

    public function getPointTypeEnumAttribute()
    {
        return $this->service_type->pointTypes();
    }

    public function scopeInBounds(Builder $query, $bounds): Builder
    {
        $polygon = [
            $bounds['northEast']['lat'] . ' ' . $bounds['northEast']['lng'],
            $bounds['northWest']['lat'] . ' ' . $bounds['northWest']['lng'],
            $bounds['southWest']['lat'] . ' ' . $bounds['southWest']['lng'],
            $bounds['southEast']['lat'] . ' ' . $bounds['southEast']['lng'],
            $bounds['northEast']['lat'] . ' ' . $bounds['northEast']['lng'],
        ];
        $boundarySearch = "ST_WITHIN(POINT(latitude, longitude), ST_GEOMETRYFROMTEXT('POLYGON((" . implode(', ', $polygon) . "))'))";

        return $query->where(DB::raw($boundarySearch), 1);
    }
}
