<?php

declare(strict_types=1);

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   bib
 * @Last Modified time: 2023-10-03 12:04:39
 */

namespace App\Models;

use App\Models\RecycleMaterial as RecycleMaterialModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecycleMaterialAlias extends Model
{
    use HasFactory;

    protected $table = 'material_aliases';

    protected $fillable = ['alias'];

    public function getParent()
    {
        return $this->belongsTo(RecycleMaterialModel::class, 'parent');
    }
}
