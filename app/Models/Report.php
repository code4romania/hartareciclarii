<?php

declare(strict_types=1);

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-23 19:46:59
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $guarded = ['id'];

    protected $casts = [
        'form_data' => 'array',
        'results' => 'array',
    ];
}
