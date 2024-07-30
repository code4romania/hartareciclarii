<?php

declare(strict_types=1);

namespace App\Models;

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
        'status',
        'source',
        'county_id',
        'city_id',
        'created_by',
        'point_group_id',
        'address',
        'location',
        'notes',
        'administered_by',
        'name',
        'phone',
        'email',
        'website',
        'observations',
        'schedule',
        'offers_money',
        'offers_vouchers',
        'offers_transport',
        'free_of_charge',
        'service_type_id',
        'point_type_id',
    ];

    protected $casts = [
        'schedule' => 'array',
        'status' => Status::class,
        'location' => SpatialPoint::class,
        'offers_money' => 'boolean',
        'offers_vouchers' => 'boolean',
        'offers_transport' => 'boolean',
        'free_of_charge' => 'boolean',

    ];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class);
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

    public function pointGroup(): BelongsTo
    {
        return $this->belongsTo(PointGroup::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function pointType(): BelongsTo
    {
        return $this->belongsTo(PointType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function changeStatus(Status $status): void
    {
        $this->update(['status' => $status]);
    }

    public function changeGroup(int $groupId): void
    {
        $this->update(['point_group_id' => $groupId]);
    }
}
