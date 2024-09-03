<?php

declare(strict_types=1);

namespace App\Models;

use App\DataTransferObjects\MapCoordinates;
use App\Enums\Point\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use MatanYadaev\EloquentSpatial\Objects\Point as SpatialPoint;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class Point extends Model
{
    use HasFactory;
    use HasSpatial;
    use Searchable;

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
        'business_name',
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

    public function scopeWhereMatchesCoordinates(Builder $query, MapCoordinates $mapCoordinates): Builder
    {
        return $query->whereWithin('location', $mapCoordinates->getBounds())
            ->orderByDistance('location', $mapCoordinates->getCenter());
    }

    public function url(): Attribute
    {
        return Attribute::make(
            fn () => route('front.map.point', [
                'point' => $this,
                'coordinates' => "@{$this->location->latitude},{$this->location->longitude},18z",
            ])
        );
    }

    public function changeStatus(Status $status): void
    {
        $this->update(['status' => $status]);
    }

    public function changeGroup(int $groupId): void
    {
        $this->update(['point_group_id' => $groupId]);
    }

    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with([
            'serviceType:id,name',
            'pointType:id,name',
            'materials:id,name',
            'materials.categories:id,name',
            'city:id,name',
            'county:id,name',
        ]);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'point_id' => (string) $this->id,
            'location' => [
                $this->location->latitude,
                $this->location->longitude,
            ],
            'service_type' => $this->serviceType->name,
            'address' => $this->address,
            'point_type' => $this->pointType->name,
            'materials' => $this->materials
                ->pluck('name'),

            'materials_categories' => $this->materials
                ->pluck('categories.*.name')
                ->flatten(),

            'administered_by' => $this->administered_by,
            'email' => $this->email,
            'phone' => $this->phone,
            'observations' => $this->observations,

            'business_name' => $this->business_name,
            'city' => $this->city->name,
            'county' => $this->county->name,

            'created_at' => $this->created_at->timestamp,
        ];
    }

    public static function getTypesenseModelSettings(): array
    {
        return [
            'collection-schema' => [
                'fields' => [
                    [
                        'name' => 'id',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'point_id',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'location',
                        'type' => 'geopoint',
                    ],
                    [
                        'name' => 'service_type',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'point_type',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'address',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'materials',
                        'type' => 'string[]',
                    ],
                    [
                        'name' => 'materials_categories',
                        'type' => 'string[]',
                    ],
                    [
                        'name' => 'administered_by',
                        'type' => 'string',
                        'optional' => true,
                    ],
                    [
                        'name' => 'email',
                        'type' => 'string',
                        'optional' => true,
                    ],
                    [
                        'name' => 'phone',
                        'type' => 'string',
                        'optional' => true,
                    ],
                    [
                        'name' => 'observations',
                        'type' => 'string',
                        'optional' => true,
                    ],
                    [
                        'name' => 'business_name',
                        'type' => 'string',
                        'optional' => true,
                    ],
                    [
                        'name' => 'city',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'county',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'created_at',
                        'type' => 'int64',
                    ],
                ],
                'default_sorting_field' => 'created_at',
            ],
            'search-parameters' => [
                'query_by' => 'point_id, service_type, point_type, address, materials, materials_categories, administered_by, email, phone, observations, business_name, city, county',
            ],
        ];
    }
}
