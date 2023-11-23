<?php

    /*
     * @Author: bib
     * @Date:   2023-10-03 10:55:55
     * @Last Modified by:   Bogdan Bocioaca
     * @Last Modified time: 2023-11-22 11:12:48
     */

namespace App\Models;

    use App\Enums\IssueStatus as IssueStatusEnum;
    use App\Models\ActionLog as ActionLogModel;
    use App\Models\MapPointField as MapPointFieldModel;
    use App\Models\MapPointGroup as MapPointGroupModel;
    use App\Models\MapPointIssues as MapPointIssuesModel;
    use App\Models\MapPointService as MapPointServiceModel;
    use App\Models\MapPointToField as MapPointToFieldModel;
    use App\Models\MapPointType as MapPointTypeModel;
    use App\Models\MaterialToMapPoint as MaterialToMapPointModel;
    use App\Models\RecycleMaterial as RecycleMaterialModel;
    use App\Models\User as UserModel;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Collection;
    use Spatie\MediaLibrary\HasMedia;
    use Spatie\MediaLibrary\InteractsWithMedia;
    use Spatie\MediaLibrary\MediaCollections\Models\Media;

    class MapPoint extends Model implements hasMedia
    {
        use InteractsWithMedia;

        protected $table = 'recycling_points';

        protected $fillable = ['name'];

        protected $hidden = ['location'];

        public function type()
        {
            return $this->hasOne(MapPointTypeModel::class, 'id', 'point_type_id');
        }

        public function fields()
        {
            return $this->hasMany(
                MapPointToFieldModel::class,
                'recycling_point_id',
                'id',
            );
        }

        public function county()
        {
            return $this->hasOne(
                MapPointToFieldModel::class,
                'recycling_point_id',
                'id',
            )->whereFieldTypeId(2);
        }

        public function getNotesAttribute()
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 10)->first())
            {
                return $this->fields->where('field_type_id', 10)->first()->value;
            }

            return '-';
        }

        public function getCityAttribute()
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 1)->first())
            {
                return $this->fields->where('field_type_id', 1)->first()->value;
            }

            return '-';
        }

        public function getAddressAttribute()
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 4)->first())
            {
                return $this->fields->where('field_type_id', 4)->first()->value;
            }

            return '-';
        }

        public function getPhoneNoAttribute()
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 12)->first())
            {
                return $this->fields->where('field_type_id', 12)->first()->value;
            }

            return '-';
        }

        public function getDetailsAttribute()
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 10)->first())
            {
                return $this->fields->where('field_type_id', 10)->first()->value;
            }

            return '-';
        }

        public function getOpeningHoursAttribute()
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 7)->first())
            {
                return json_decode($this->fields->where('field_type_id', 7)->first()->value, true);
            }

            return [];
        }

        public function materials()
        {
            return $this->hasManyThrough(
                RecycleMaterialModel::class,
                MaterialToMapPointModel::class,
                'recycling_point_id',
                'id',
                'id',
                'material_id'
            );
        }

        public function issues()
        {
            return $this->hasMany(
                MapPointIssuesModel::class,
                'point_id',
                'id',
            )->where('status', IssueStatusEnum::New);
        }

        public function group()
        {
            return $this->belongsTo(
                MapPointGroupModel::class,
                'group_id',
                'id',
            );
        }

        public function getGod()
        {
            return $this->belongsTo(
                UserModel::class,
                'created_by',
                'id',
            );
        }

        public function scopeManageble(Builder $query): void
        {
            if (!auth()->user()->hasRole('SuperAdmin'))
            {
                $query->where('created_by', auth()->user()->id);
            }
        }

        public function changeStatus(): bool
        {
            $action = collect([
                'model' => \get_called_class(),
                'model_id' => $this->id,
                'user_id' => auth()->user()->id,
                'action' => 'change_status',
                'old_values' => ['status' => (int) $this->status],
                'new_values' => ['status' => (int) !$this->status],
            ]);
            $this->status = !$this->status;
            $this->save();

            ActionLogModel::logAction($action);

            return true;
        }

        public function changeGroup(int $group_id): bool
        {
            $action = collect([
                'model' => \get_called_class(),
                'model_id' => $this->id,
                'user_id' => auth()->user()->id,
                'action' => 'change_group',
                'old_values' => ['group' => ($this->group) ? $this->group->name : ''],

            ]);
            $this->group()->associate($group_id);
            $this->save();
            $action->put('new_values', ['group' => $this->group->name]);

            ActionLogModel::logAction($action);

            return true;
        }

        public function getWebsiteAttribute($value)
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 9)->first())
            {
                return $this->fields->where('field_type_id', 9)->first()->value;
            }

            return '-';

            return '-';
        }

        public function getManagedByAttribute($value)
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 3)->first())
            {
                return $this->fields->where('field_type_id', 3)->first()->value;
            }

            return '-';
        }

        public function getLocationNotesAttribute($value)
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 13)->first())
            {
                return $this->fields->where('field_type_id', 13)->first()->value;
            }

            return '-';
        }

        public function getEmailAttribute($value)
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 8)->first())
            {
                return $this->fields->where('field_type_id', 8)->first()->value;
            }

            return '-';
        }

        // public function getMaterialsAttribute($value)
        // {
        //     if ($this->materials)
        //     {
        //         return implode(';', $this->materials->pluck('name')->toArray());
        //     }

        //     return '-';
        // }

        public function getMaterialsIconAttribute($value)
        {
            if ($this->materials)
            {
                $icons = $this->materials->pluck('icon')->toArray();
                $state = '<div style="display:inline-flex">';
                foreach ($icons as $icon)
                {
                    $state .= __("<img style='width:30px;padding:5px' src='" . str_replace(' ', '', $icon) . "'>");
                }
                $state = rtrim($state) . '</div>';

                return $state;
            }

            return '-';
        }

        public function getCountyAttribute()
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 2)->first())
            {
                return $this->fields->where('field_type_id', 2)->first()->value;
            }

            return '-';
        }

        public function getOffersTransportAttribute(): int
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 6)->first())
            {
                if (strtolower($this->fields->where('field_type_id', 6)->first()->value) == 'nu')
                {
                    return 0;
                }

                return 1;
            }

            return 0;
        }

        public function getOffersMoneyAttribute(): int
        {
            if ($this->fields->isNotEmpty() && $this->fields->where('field_type_id', 5)->first())
            {
                if (strtolower($this->fields->where('field_type_id', 5)->first()->value) == 'nu')
                {
                    return 0;
                }

                return 1;
            }

            return 0;

            return 0;
        }

        public function getRows(): array
        {
            $apiCall = Http::asJson()
                ->acceptJson()
                // This uses a local API endpoint for demonstration purposes.
                // You can use any API endpoint you want
                ->get(config('app.url') . '/api/subscription');

            return $apiCall->json('data');
        }

        public function getActionLogs()
        {
            return $this->hasMany(
                ActionLogModel::class,
                'model_id',
                'id',
            )->where('model', \get_called_class());
        }

        public function address()
        {
            return $this->hasOne(
                MapPointToFieldModel::class,
                'recycling_point_id',
                'id',
            )->whereFieldTypeId(4);
        }

        public function locationNotes()
        {
            return $this->hasOne(
                MapPointToFieldModel::class,
                'recycling_point_id',
                'id',
            )->whereFieldTypeId(13);
        }

        public function updateAddress(Collection $data): self
        {
            $location_fields = [
                'lat', 'lon', 'address', 'location_notes',
            ];
            $old_values = [];
            $new_values = [];
            foreach ($location_fields as $field)
            {
                if ($this->$field != $data->get($field))
                {
                    $old_values[$field] = $this->$field;
                    $new_values[$field] = $data->get($field);
                }
            }
            $this->location = \DB::raw('GeomFromText("POINT(' . data_get($data, 'lat') . ' ' . data_get($data, 'lon') . ')")');
            $this->save();
            $judet = \DB::select(\DB::raw('SELECT * FROM judete_geo jg WHERE ST_CONTAINS(jg.pol, Point(' . data_get($data, 'lon') . ', ' . data_get($data, 'lat') . '))')->getValue(\DB::connection()->getQueryGrammar()));
            if (!empty($judet))
            {
                $field = collect([
                    'field_type_id' => 2,
                    'recycling_point_id' => $this->id,
                    'value' => $judet[0]->name,
                ]);
                MapPointToFieldModel::addValueToPoint($field);
            }
            $action = collect([
                'model' => \get_called_class(),
                'model_id' => $this->id,
                'user_id' => auth()->user()->id,
                'action' => 'location_update',
                'old_values' => $old_values,
                'new_values' => $new_values,

            ]);
            $this->lat = $data->get('lat');
            $this->lon = $data->get('lon');

            $field_value = collect(
                ['field_type_id' => 13,
                    'recycling_point_id' => $this->id,
                    'value' => $data->get('location_notes'),
                ]
            );
            $location_notes = MapPointToFieldModel::addValueToPoint($field_value);

            $field_value = collect([
                'field_type_id' => 4,
                'recycling_point_id' => $this->id,
                'value' => $data->get('address'),
            ]);
            $address = MapPointToFieldModel::addValueToPoint($field_value);

            $this->save();
            ActionLogModel::logAction($action);

            return $this;
        }

        public function updateDetails(Collection $data): bool
        {
            $location_fields = [
                'type', 'materials', 'managed_by', 'website', 'email', 'opening_hours', 'notes', 'offers_transport', 'offers_money', 'phone_no',
            ];
            $old_values = [];
            $new_values = [];
            foreach ($location_fields as $field)
            {
                if ($field == 'materials')
                {
                    $materials = $this->materials->pluck('id')->toArray();
                    $new_materials = $data->get('materials');

                    if (array_diff($materials, $new_materials) || array_diff($new_materials, $materials))
                    {
                        $old_values[$field] = $materials;
                        $new_values[$field] = $data->get('materials');
                    }
                }
                elseif ($field == 'opening_hours')
                {
                    $result = $this->checkArrayDiffMulti($this->opening_hours, $data->get('opening_hours'));
                    if (\count($result) > 0)
                    {
                        $old_values[$field] = json_encode($this->opening_hours);
                        $new_values[$field] = json_encode($data->get('opening_hours'));
                    }
                }
                elseif ($field == 'type')
                {
                    if ($this->type->id != $data->get('type'))
                    {
                        $old_values[$field] = $this->type->display_name;
                        $new_values[$field] = MapPointTypeModel::find($data->get('type'))->display_name;
                    }
                }
                elseif ($this->$field != $data->get($field))
                {
                    $old_values[$field] = $this->$field;
                    $new_values[$field] = $data->get($field);
                }
            }

            $action = collect([
                'model' => \get_called_class(),
                'model_id' => $this->id,
                'user_id' => auth()->user()->id,
                'action' => 'details_update',
                'old_values' => $old_values,
                'new_values' => $new_values,
            ]);
            ActionLogModel::logAction($action);
            $fields = [
                collect([
                    'field_type_id' => 4,
                    'recycling_point_id' => $this->id,
                    'value' => $data->get('address'),
                ]),
                collect([
                    'field_type_id' => 3,
                    'recycling_point_id' => $this->id,
                    'value' => $data->get('managed_by'),
                ]),
                collect([
                    'field_type_id' => 9,
                    'recycling_point_id' => $this->id,
                    'value' => $data->get('website'),
                ]),
                collect([
                    'field_type_id' => 8,
                    'recycling_point_id' => $this->id,
                    'value' => $data->get('email'),
                ]),
                collect([
                    'field_type_id' => 7,
                    'recycling_point_id' => $this->id,
                    'value' => json_encode($data->get('opening_hours', [])),
                ]),
                collect([
                    'field_type_id' => 10,
                    'recycling_point_id' => $this->id,
                    'value' => $data->get('notes'),
                ]),
                collect([
                    'field_type_id' => 6,
                    'recycling_point_id' => $this->id,
                    'value' => (int) $data->get('offers_transport', 0),
                ]),
                collect([
                    'field_type_id' => 5,
                    'recycling_point_id' => $this->id,
                    'value' => (int) $data->get('offers_money', 0),
                ]),
                collect([
                    'field_type_id' => 12,
                    'recycling_point_id' => $this->id,
                    'value' => $data->get('phone_no'),
                ]),
            ];
            MapPointToFieldModel::addValuesToPoint($fields);
            $this->point_type_id = $data->get('type', 0);
            $this->save();

            $this->point_type_id = $data->get('type');
            $this->save();

            return true;
        }

        public function checkArrayDiffMulti($array1, $array2)
        {
            $result = [];
            if (\count($array1) < \count($array2))
            {
                return $array2;
            }
            foreach ($array1 as $key => $val)
            {
                if (isset($array2[$key]))
                {
                    if (\is_array($val) && $array2[$key])
                    {
                        $diff = $this->checkArrayDiffMulti($val, $array2[$key]);
                        if (\count($diff) > 0)
                        {
                            $result[$key] = $this->checkArrayDiffMulti($val, $array2[$key]);
                        }
                    }
                    else
                    {
                        if ($array1[$key] != $array2[$key])
                        {
                            $result[$key] = $val;
                        }
                    }
                }
                else
                {
                    $result[$key] = $val;
                }
            }

            return $result;
        }

        // public function getGroupAttribute($value)
        // {
        //     if ($this->group)
        //     {
        //         $this->group->name;
        //     }

        //     return '-';
        // }

        public function getStatusBadgeAttribute($value)
        {
            if ($this->issues->count() > 0)
            {
                $color = 'danger';
            }
            if ((int) $this->status === 1)
            {
                $color = 'success';
            }

            $color = 'warning';
            if ($this->issues->count() > 0)
            {
                $text = __('map_points.issues_found');
            }
            if ((int) $this->status === 1)
            {
                $text = __('map_points.verified');
            }

            $text = __('map_points.requires_verification');
            $badge = '<x-filament::badge color="' . $color . '">
		' . $text . '
		</x-filament::badge>';

            return $badge;
        }

        public function service()
        {
            return $this->hasOne(MapPointServiceModel::class, 'id', 'service_id');
        }

        public static function createFromArray(array $data): self
        {
			$geoLocation = Geolocation::reverse(data_get($data, 'lat'), data_get($data, 'lon'));
			dd($geoLocation);
			
            $record = new self();
            $record->service_id = data_get($data, 'service_id');
            $record->point_type_id = data_get($data, 'type_id');
            $record->lat = !empty($geoLocation) ? $geoLocation['lat'] :data_get($data, 'lat');
            $record->lon = !empty($geoLocation) ? $geoLocation['lon'] :data_get($data, 'lon');
            $record->id_county = !empty($geoLocation) ? $geoLocation['county_id'] : 0;
            $record->id_city = !empty($geoLocation) ? $geoLocation['city_id'] : 0;
            $record->location = \DB::raw('ST_GeomFromText("POINT(' . data_get($data, 'lon') . ' ' . data_get($data, 'lat') . ')")');
            $record->created_by = data_get($data, 'created_by');
            $record->point_source = data_get($data, 'point_source', 'user');
            $record->status = 0;
			
            $record->save();

            foreach (MapPointFieldModel::all() as $field)
            {
                if (in_array($field->field_name, ['county', 'city']))
                {
                    continue;
                }
                $fields[] = collect([
                    'field_type_id' => $field->id,
                    'recycling_point_id' => $record->id,
                    'value' => data_get($data, $field->field_name),
                ]);
            }
            
            if (!empty($fields))
            {
                MapPointToFieldModel::addValuesToPoint($fields);
            }

            if (!empty(data_get($data, 'materials')))
            {
                foreach (data_get($data, 'materials') as $material_id)
                {
                    $item = MaterialToMapPointModel::firstOrCreate(['material_id' => $material_id, 'recycling_point_id' => $record->id]);
                }
            }

            if (!empty(data_get($data, 'photos')))
            {
                foreach (data_get($data, 'photos') as $photo)
                {
                    $record->addMediaFromDisk($photo)->toMediaCollection('point_images');
                }
            }

            $action = collect([
                'model' => self::class,
                'model_id' => $record->id,
                'user_id' => data_get($data, 'created_by'),
                'action' => 'created',
                'old_values' => [],
                'new_values' => [],
            ]);

            ActionLogModel::logAction($action);

            return $record;
        }

        public function fieldTypes()
        {
            return $this->hasManyThrough(
                MapPointFieldModel::class,
                MapPointToFieldModel::class,
                'recycling_point_id',
                'id',
                'id',
                'field_type_id'
            );
        }

        /**
         * Returns the list of available map points for the given filters.
         *
         * @param array $filters
         *
         * @return Collection
         */
        public static function getFilteredMapPoints(array $filters, array $bounds): Collection
        {
            if (empty($bounds))
            {
                return collect([]);
            }

            $polygon = [
                $bounds['northEast']['lat'] . ' ' . $bounds['northEast']['lng'],
                $bounds['northWest']['lat'] . ' ' . $bounds['northWest']['lng'],
                $bounds['southWest']['lat'] . ' ' . $bounds['southWest']['lng'],
                $bounds['southEast']['lat'] . ' ' . $bounds['southEast']['lng'],
                $bounds['northEast']['lat'] . ' ' . $bounds['northEast']['lng'],
            ];
            $boundarySearch = "ST_WITHIN(POINT(recycling_points.lat, recycling_points.lon), ST_GEOMETRYFROMTEXT('POLYGON((" . implode(', ', $polygon) . "))')) AS in_bounds";
            $sql = self::
            select(
                'recycling_points.id',
                'recycling_points.lat',
                'recycling_points.lon',
                'recycling_points.location',
                'recycling_point_services.icon',
                \DB::raw($boundarySearch)
            )
                ->having('in_bounds', '=', 1)
                ->where('status', 1)
                ->join('recycling_point_services', 'recycling_point_services.id', '=', 'recycling_points.service_id');

            if (!empty($filters))
            {
                foreach ($filters as $key => $value)
                {
                    match ($key)
                    {
                        'service_id' => $sql->where('recycling_points.service_id', $value),
                        'point_type_id' => $sql->whereIn('recycling_points.point_type_id', (array) $value),
                        'material_type_id' => $sql->whereHas('materials', function ($query) use ($value)
                        {
                            $query->whereIn('material_recycling_point.material_id', (array) $value);
                        }),
                        'field_type_id' => $sql->whereHas('fieldTypes', function ($query) use ($value)
                        {
                            $query->whereIn('field_type_recycling_point.field_type_id', (array) $value);
                        }),
                        'search_key' => $sql->whereHas('fieldTypes', function ($query) use ($value)
                        {
                            $query->where('field_type_recycling_point.value', 'LIKE', '%' . $value . '%');
                        }),
                    };
                }
            }
            //$sql->limit(50);

            return $sql->get();
        }

        /**
         * @param array $data
         *
         * @return self
         */
        public static function create(array $data): self
        {
            $point = [
                'service_id' => data_get($data, 'service_id'),
                'type_id' => data_get($data, 'point_type_id'),
                'lat' => data_get($data, 'lat'),
                'lon' => data_get($data, 'lng'),
                'created_by' => data_get($data, 'id_user'),
                'point_source' => 'user',
                'materials' => (array) data_get($data, 'material_recycling_point'),
                'photos' => (array) data_get($data, 'photos'),
            ];

            foreach (data_get($data, 'field_types') as $key => $value)
            {
                $point[$key] = $value;
            }

            return self::createFromArray($point);
        }

        public function registerMediaCollections(): void
        {
            $this->addMediaCollection('point_images')
                ->singleFile()
                ->registerMediaConversions(function (Media $media)
                {
                    $this
                        ->addMediaConversion('preview');
                });
        }
    }
