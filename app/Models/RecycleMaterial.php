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
}
