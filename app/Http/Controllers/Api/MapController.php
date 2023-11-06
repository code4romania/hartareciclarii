<?php

namespace App\Http\Controllers\Api;

use App\Models\Issue;
use App\Models\IssueType;
use App\Models\MapPoint;
use App\Models\MapPointField;
use App\Models\MapPointService;
use App\Models\RecycleMaterial;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\IssueTypeCollection;

class MapController extends Controller
{
	public function points(Request $request)
	{
		$filters = $request->get('filters', []);
		$bounds = $request->get('bounds', []);

		return response()
			->json(
			[
				'points' => MapPoint::getFilteredMapPoints($filters, $bounds)
			]);
	}

	public function create(Request $request)
	{
		$data = $request->all();
		$userToken = $request->headers->get('authorization', '');
		$id_user = null;
		if ($userToken != "")
		{
			if (Auth::guard('sanctum')->check())
			{
				$user = Auth::guard('sanctum')->user();
				if ($user)
				{
					$id_user = $user->id;
				}
			}
		}
		$data['id_user'] = $id_user;

		$this->validate($request, [
			'point_type_id' => [
				'required',
				'int',
				'exists:recycling_point_types,id',
			],
			'lat' => [
				'required',
				'numeric',
				'between:-90,90'
			],
			'lng' => [
				'required',
				'numeric',
				'between:-180,180'
			],
			'field_types.managed_by' => [
				'string'
			],
			'field_types.address' => [
				'required',
				'string'
			],
			'field_types.website' => [
				'url',
                'nullable'
			],
			'field_types.email' => [
				'email',
                'nullable'
			],
			'material_recycling_point' => [
				'array'
			],
			'material_recycling_point.*' => [
				'int',
				'exists:materials,id',
			]
		]);

		$point = MapPoint::create($data);
		return response()
			->json($point);
	}

	public function nomenclatures(Request $request)
	{
		return response()
			->json(
				[
					'services' => MapPointService::with('pointTypes')->get(),
					'material_recycling_points' => RecycleMaterial::whereNull('is_wildcard')->get(),
					'field_types' => MapPointField::all(),
					//'reported_point_issue_types' => IssueTypeCollection::collection(IssueType::all()),
					'reported_point_issue_types' => IssueType::with('items')->get(),
				]);
	}

	public function point(int $id)
	{
		return response()
			->json(
				[
					'point' => MapPoint::with('type', 'service', 'fields.field', 'materials')->find($id),
					'materials' => RecycleMaterial::whereNull('is_wildcard')->whereNull('parent')->get(),
				]);
	}
	
	public function report(int $point_id, Request $request)
	{
		$request->offsetSet('point_id', $point_id);
		$data = $request->all();
		$userToken = $request->headers->get('authorization', '');
		$id_user = null;
		if ($userToken != "")
		{
			if (Auth::guard('sanctum')->check())
			{
				$user = Auth::guard('sanctum')->user();
				if ($user)
				{
					$id_user = $user->id;
				}
			}
		}
		$data['id_user'] = $id_user;
		
		$rules = $this->_getIssueValidationRules($data);
		$this->validate($request, $rules);
		
		$point = Issue::createFromArray($data);
		return response()
			->json($point);
	}
	
	private function _getIssueValidationRules($data)
	{
		$rules['point_id'] =
		[
			'required',
			'int',
			'exists:recycling_points,id',
		];
		
		$rules['reported_point_issue_type_id'] =
		[
			'required',
			'int',
			'exists:reported_point_issue_types,id',
		];
		
		switch ($data['reported_point_issue_type_id'])
		{
			case 1:
			case 2:
				$rules['lat'] =
				[
					'required',
					'numeric',
					'between:-90,90'
				];
			
				$rules['lng'] =
				[
					'required',
					'numeric',
					'between:-180,180'
				];
			break;
			case 3:
				$rules['material_issue_extra'] =
				[
					'array',
				];
				
				$rules['material_issue_extra.*'] =
				[
					'int',
					'exists:materials,id',
				];
				
				$rules['material_issue_missing'] =
				[
					'array',
				];
				
				$rules['material_issue_missing.*'] =
				[
					'int',
					'exists:materials,id',
				];
				
				$rules['material_issue'] =
				[
					'array',
					'required'
				];
				
				$rules['material_issue.*'] =
				[
					'int',
					'exists:reported_point_issue_type_items,id',
				];
			break;
			
			case 6:
				$rules['description'] =
				[
					'required',
					'string'
				];
				
				$rules['collection_decline_reason'] =
				[
					'array',
				];
				
				$rules['collection_decline_reason.*'] =
				[
					'int',
					'exists:reported_point_issue_type_items,id',
				];
			break;
			
			case 4:
			case 5:
			case 7:
				$rules['description'] =
				[
					'required',
					'string'
				];
				
			break;
		}
		
		return $rules;
	}
}

