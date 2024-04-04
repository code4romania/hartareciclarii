<?php

declare(strict_types=1);

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-03 20:23:11
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapPointGroup extends Model
{
    protected $table = 'recycling_points_groups';

    protected $fillable = ['name'];
}
