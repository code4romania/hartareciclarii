<?php

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-31 12:23:45
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueType extends Model
{
    protected $table = 'reported_point_issue_types';
	protected $casts = [
		'steps' => 'array'
	];
	
	protected $fillable = [
		'id'
	];
	public function items()
	{
		return $this->hasMany(IssueTypeItem::class, 'reported_point_issue_type_id', 'id');
	}
}
