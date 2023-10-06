<?php

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-06 11:41:57
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapPointService extends Model
{
    protected $table = 'recycling_point_services';

    protected $fillable = ['name'];
}
