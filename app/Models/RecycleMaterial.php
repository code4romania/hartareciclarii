<?php

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   bib
 * @Last Modified time: 2023-10-03 12:06:34
 */

namespace App\Models;

use App\Models\RecycleMaterialAlias as RecycleMaterialAliasModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RecycleMaterial extends Model
{
	use HasFactory;
    protected $table = 'materials';

    protected $fillable = ['name'];

    public function getParent()
    {
        return $this->belongsTo(self::class, 'parent');
    }

    public function aliases()
    {
        return $this->hasMany(RecycleMaterialAliasModel::class, 'parent');
    }
	
	public static function getAvailableMaterialsOnServiceId(int $service_id) : Collection
	{
		$sql = self::
			select(
				'materials.*',
				\DB::raw('IFNULL(materials.order, 10000) AS `order`')
			)
			->whereNull('is_wildcard')
			//->whereRaw('id IN (SELECT material_id FROM material_recycling_point mrp, recycling_points rp WHERE mrp.recycling_point_id = rp.id AND rp.service_id = '.$service_id.')')
			->orderBy('order', 'ASC');
		
		return $sql->get();
	}
}
