<?php

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-26 17:44:26
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $table = 'reported_point_issues';
	
	public function type()
	{
		return $this->hasOne(IssueType::class, 'id', 'reported_point_issue_type_id');
	}
	
	public static function createFromArray($data)
	{
		$issue = new self();
		$issue->point_id = $data['point_id'];
		$issue->reporter_id = $data['id_user'];
		$issue->status = 0;
		$issue->reported_point_issue_type_id = $data['reported_point_issue_type_id'];
		$issue->description = self::_fillDescription($data);
		$issue->created_at = Carbon::now()->toDateTimeString();
		
		switch ($data['reported_point_issue_type_id'])
		{
			case 1:
			case 2:
				$issue->latitude = $data['lat'];
				$issue->longitude = $data['lng'];
			break;
			
			case 3:
				if (isset($data['material_issue']))
				{
					$issue->material_issue = $data['material_issue'];
				}
				
				if (isset($data['material_issue_missing']))
				{
					$issue->material_issue_missing = $data['material_issue_missing'];
				}
				
				if (isset($data['material_issue_extra']))
				{
					$issue->material_issue_extra = $data['material_issue_extra'];
				}
				
			break;
			
			case 6:
				if (isset($data['collection_decline_reason']))
				{
					$issue->collection_decline_reason = $data['collection_decline_reason'];
				}
			break;
		}
		
		
		$issue->save();
		
		return $issue;
	}
	
	private static function _fillDescription($data)
	{
		$description = '';
		if (isset($data['description']))
		{
			$description = $data['description'];
		}
		
		if (isset($data['address']))
		{
			$description = $data['address'];
		}
		
		return $description;
	}
}
