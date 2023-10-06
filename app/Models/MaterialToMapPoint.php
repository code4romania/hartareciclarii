<?php

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-06 17:55:04
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialToMapPoint extends Model
{
    protected $table = 'material_recycling_point';

    protected $fillable = ['name', 'material_id', 'recycling_point_id'];
}
