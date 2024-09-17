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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use MatanYadaev\EloquentSpatial\Objects\Point as SpatialPoint;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Point extends Model implements HasMedia
{
    use HasFactory;
    use HasSpatial;
    use Searchable;
    use SoftDeletes;
    use InteractsWithMedia;

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
        'import_id',
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->fit(Fit::Crop, 96, 96)
                    ->keepOriginalImageFormat()
                    ->optimize();
            });
    }

    public function materials(): MorphToMany
    {
        return $this->morphToMany(Material::class, 'model', 'model_has_materials');
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

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
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

            'material_categories' => $this->materials
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
                        'name' => 'material_categories',
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
                'query_by' => 'point_id, service_type, point_type, business_name, administered_by, address, materials, material_categories, city, county, email, phone, observations',
            ],
        ];
    }
}
