<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FilterablePointTypes extends Model
{
    protected $table = 'filterable_field_types';

    protected $fillable = ['name'];
	
	public function fieldTypes() : HasMany
	{
		return $this->hasMany(MapPointField::class, 'id', 'field_type_id');
	}
}
