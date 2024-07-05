<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Point\ServiceType;
use App\Enums\Point\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\Point as SpatialPoint;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class Point extends Model
{
    use HasFactory;
    use HasSpatial;

    protected $fillable = [
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
        'location',
    ];

    protected $casts = [
        'schedule' => 'array',
        'status' => Status::class,
        'service_type' => ServiceType::class,
        'location' => SpatialPoint::class,
    ];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class);
    }

    public function getPointTypeEnumAttribute()
    {
        return $this->service_type->pointTypes();
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }


}
