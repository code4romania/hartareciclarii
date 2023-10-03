<?php

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-03 20:17:37
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapPointType extends Model
{
    protected $table = 'recycling_point_types';

    protected $fillable = ['name'];
}
