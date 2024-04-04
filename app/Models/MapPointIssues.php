<?php

declare(strict_types=1);

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-03 18:02:01
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapPointIssues extends Model
{
    protected $table = 'reported_point_issues';

    protected $fillable = ['name'];
}
