<?php

namespace App\Http\Controllers\Api;

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
}
