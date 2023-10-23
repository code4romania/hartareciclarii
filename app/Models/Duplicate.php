<?php

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-23 13:09:21
 */

namespace App\Models;

use App\Models\MapPoint as MapPointModel;
use App\Models\MapPointType as MapPointTypeModel;
use Illuminate\Database\Eloquent\Model;

class Duplicate extends Model
{
    protected $table = 'recycle_point_duplicates';

    public function type()
    {
        return $this->hasOne(MapPointTypeModel::class, 'id', 'point_type_id');
    }

    public function firstPoint()
    {
        return $this->belongsTo(MapPointModel::class, 'recycle_point_1');
    }

    public function secondPoint()
    {
        return $this->belongsTo(MapPointModel::class, 'recycle_point_2');
    }
}
