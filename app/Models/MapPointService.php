<?php

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-06 11:41:57
 */

namespace App\Models;

use App\Http\Resources\MapPointTypeResource;
use App\Models\MapPointToField as MapPointToFieldModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class MapPointService extends Model
{
    protected $table = 'recycling_point_services';

    protected $fillable = ['name'];
	
	public static function getExtendedFilters(int $service_id) : Collection
	{
		$extendedFilters = [];
		if ($service_id > 0)
		{
			$extendedFilters['service_types'] = MapPointTypeResource::collection(MapPointType::where('service_id', $service_id)->get());
			$extendedFilters['material_types'] = ($service_id == 1) ? RecycleMaterial::getAvailableMaterialsOnServiceId($service_id) : [];
			$extendedFilters['features'] = FilterablePointTypes::where('service_id', $service_id)->with('fieldTypes')->get();
		}
		
		return collect($extendedFilters);
	}
	
	public function pointTypes()
	{
		return $this->hasMany(
			MapPointType::class,
			'service_id',
			'id',
		);
	}
}
