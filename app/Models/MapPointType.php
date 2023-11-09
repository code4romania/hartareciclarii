<?php

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-11-09 17:50:31
 */

namespace App\Models;

use App\Models\MapPoint as MapPointModel;
use Illuminate\Database\Eloquent\Model;

class MapPointType extends Model
{
    protected $table = 'recycling_point_types';

    protected $fillable = ['name'];

    public function map_points()
    {
        return $this->hasMany(MapPointModel::class, 'point_type_id');
    }
}
